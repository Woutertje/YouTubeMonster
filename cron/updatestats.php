<?php
	include('../core/config.php');
	$GLOBALS['config']['classesroot'] = '../classes/';
	$GLOBALS['config']['templateroot'] = '../templates/';
	$GLOBALS['config']['pagesroot'] = '../pages/';
	include('../core/startup.php');
	
	# Record generation time
	$t = strtotime('now') + microtime();
	
	# Global vars
	global $db;
	
	# Montly totals
	$thismonthstart = mktime(0, 0, 0, date('m'), 1);
	$thismonthend = mktime(23, 59, 59, date('m'), date('t', $first_minute));
	$prevmonth = strtotime('-1 month');
	$lastmonthstart = mktime(0, 0, 1, date('m', $prevmonth), 1, date('Y', $prevmonth));
	
	# drop old
	$db->delete('stats', array(
		'end_time<' => $lastmonthstart
	));
	
	# Grab all stats
	$dbstats = $db->query('
		SELECT
			`start_time`,
			`end_time`,
			`ip`,
			`user_id`,
			`count`
		FROM `stats`
	')->fetchAll();
	
	# Set stats array
	$stats = array();
	for($i = 0; $i <= 1; $i++){
		# Setting arrays
		$curday = array();
		
		# Curday
		$daytime = strtotime(date('j M Y', strtotime('-'.$i.' days')).' 12pm');
		$curday['info'] = date('j M Y', $daytime);
		$curday['starttime'] = $daytime - (60*60*12);
		$curday['endtime'] = $daytime + (60*60*12);
		
		# Stats for this day
		$curday['stats'] = array();
		$curday['unique'] = 0;
		$curday['bouncers'] = 0;
		
		# Add to global stats array
		$stats[] = $curday;
	}
	
	# Loop trough all users
	$total['thismonth'] = array();
	$total['lastmonth'] = array();
	foreach($dbstats as $stat){
		if($stat['end_time'] > $thismonthstart && $stat['end_time'] < $thismonthend)
			$total['thismonth'][(int)$stat['user_id']][$stat['ip']] = 1;
		else
			$total['lastmonth'][(int)$stat['user_id']][$stat['ip']] = 1;
		foreach($stats as $key => $info){
			if(($stat['start_time'] > $stats[$key]['starttime'] && $stat['start_time'] < $stats[$key]['endtime']) || ($stat['end_time'] > $stats[$key]['starttime'] && $stat['end_time'] < $stats[$key]['endtime'])){
				$stats[$key]['stats'][(int)$stat['user_id']][$stat['ip']][] = $stat['count'];
			}
		}
	}
	
	# Totals per day
	$bestday = 0;
	for($i = 0; $i <= 1; $i++){
		$days[] = '\''.$stats[$i]['info'].'\'';
		$count = count($stats[$i]['stats']);
		if(isset($stats[$i]['stats'][0])){
			$count--;
			$count += count($stats[$i]['stats'][0]);
			foreach($stats[$i]['stats'][0] as $ip => $statcounter){
				if(count($statcounter) == 1)
					if($statcounter[0] < 3)
						$stats[$i]['bouncers']++;
			}
		}
		$stats[$i]['unique'] = $count;
		if($count > $bestday)
			$bestday = $count;
	}
	
	# Calculate totals
	echo '<pre>'.print_r($total, true).'</pre>';
	foreach($total as $key => $value){
		$newtotal = count($total[$key]);
		if(isset($total[$key][0])){
			$newtotal--;
			$newtotal += count($total[$key][0]);
		}
		$total[$key] = $newtotal;
	}
	
	# Fix mistake
	if(date('M Y', $lastmonthstart) == 'Dec 2012')
		$total['lastmonth'] = 80;
	
	# Create update array
	$archive = array();
	$archive[] = array(
		'category' => 'unique users per month',
		'key' => date('M Y'),
		'val1' => $total['thismonth']
	);
	$archive[] = array(
		'category' => 'unique users per month',
		'key' => date('M Y', strtotime('-1 month')),
		'val1' => $total['lastmonth']
	);
	$archive[] = array(
		'category' => 'unique users per day',
		'key' => date('j M Y'),
		'val1' => $stats[0]['unique'],
		'val2' => $stats[0]['bouncers'],
		'val3' => $stats[0]['unique'] - $stats[0]['bouncers']
	);
	$archive[] = array(
		'category' => 'unique users per day',
		'key' => date('j M Y', strtotime('-1 day')),
		'val1' => $stats[1]['unique'],
		'val2' => $stats[1]['bouncers'],
		'val3' => $stats[1]['unique'] - $stats[1]['bouncers']
	);
	$archive[] = array(
		'category' => 'video\'s added per day',
		'key' => date('j M Y'),
		'val1' => $db->query('SELECT COUNT(*) FROM `videos` WHERE `addedon` > ? AND `addedon` < ?', array($stats[0]['starttime'], $stats[0]['endtime']))->fetchColumn()
	);
	$archive[] = array(
		'category' => 'video\'s added per day',
		'key' => date('j M Y', strtotime('-1 day')),
		'val1' => $db->query('SELECT COUNT(*) FROM `videos` WHERE `addedon` > ? AND `addedon` < ?', array($stats[1]['starttime'], $stats[1]['endtime']))->fetchColumn()
	);
	
	# Update / insert to archive
	foreach($archive as $currow){
		echo '<h3>'.ucfirst($currow['category']).': '.$currow['key'].'</h3><pre>'.print_r($currow, true).'</pre>';
		if($db->query('
			SELECT COUNT(*)
			FROM `stats_archive`
			WHERE `category` = ?
			AND `key` = ?
		', array(
			$currow['category'],
			$currow['key']
		))->fetchColumn() > 0){
			# Update archive
			$vals = $currow;
			unset($vals['category']);
			unset($vals['key']);
			$where = $currow;
			for($i = 1; $i <= 3; $i++)
				unset($where['val'.$i]);
			$db->update('stats_archive', $vals, $where);
			echo '<p>Updated stats archive</p>';
		}
		else{
			# Insert to archive
			$db->insert('stats_archive', $currow);
			echo '<p>Inserted into stats archive</p>';
		}
	}
	
	# Result time
	unset($stats);
	unset($total);
	echo '<p style="color: #D00000">Generating stats took '.number_format((((strtotime('now') + microtime()) - $t) * 1000), 2, '.', '').' ms.</p>';