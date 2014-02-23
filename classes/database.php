<?php

# ===========================================
# Database class v1.3
# Created by:
#   Rick van der Staaij
#   May 2012
# ===========================================
# $db->insert('users', array(
#   'username' => $username,
#   'password' => safepass($password)
# ));
# $db->update('users', array(
#   'username' => $newusername,
# ), array(
#   'id=' => $userid
# ));
# $db->delete('users', array(
#   'id=' => $userid
# ));
# ===========================================
# WHERE variables:
# 1. Null (all table entries)
# 1. array();
#    key: fieldname with operator:
#      = equal (default) { 'name=' => 'user'}
#      ! not equal { 'name=' => 'user'}
#      > bigger than { 'value>' => 500}
#      < smaller than { 'value<' => 500}
#      % LIKE { 'name%' => 'user'}
#    Value
# 2. string
# ===========================================

class database
{
    # ===============================
    # Variables
    # ===============================
    private
        $connection,
        $queriesexecuted = 0,
        $fetchmode = PDO::FETCH_ASSOC,
        $log = array(),
        $host,
        $user,
        $password,
        $database,
        $init = false;

    # ===============================
    # Constructor (init connection)
    # ===============================
    public function __construct($host, $user, $password, $database)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
    }

    # ===============================
    # Initialise the database
    # ===============================
    public function initDB()
    {
        if (!$this->init) {
            $this->connection = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->database,
                $this->user,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            # Set database queries to our default utf8
            $this->connection->query('SET NAMES `utf8`');
            $this->init = true;
        }
    }

    # ===============================
    # Get the connection
    # ===============================
    public function con()
    {
        return $this->connection;
    }

    # ===============================
    # Get last insert id
    # ===============================
    public function lastinsertid($name = null)
    {
        return $this->connection->lastInsertId($name);
    }

    # ===============================
    # Get the number of queries executed
    # ===============================
    public function queriesexecuted()
    {
        return $this->queriesexecuted;
    }

    # ===============================
    # Get the number of queries executed
    # ===============================
    public function getlog()
    {
        return '<pre>'.print_r($this->log, true).'</pre>';
    }

    # ===============================
    # Quote function
    # ===============================
    public function quote($value)
    {
        return $this->connection->quote($value);
    }

    # ===============================
    # Query, makes prepared statement
    # ===============================
    public function query($sql, $params = null)
    {
        $this->log[] = $sql;

        $this->initDB();

        try{
            # If no parameters use query anyway
            if($params === null){
                $stmt = $this->connection->query($sql);
                $stmt->setFetchMode($this->fetchmode);
                $this->queriesexecuted++;
                return $stmt;
            }

            # Prepared statement
            $params = (array)$params;
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            $stmt->setFetchMode($this->fetchmode);
            $this->queriesexecuted++;
            return $stmt;
        }
        catch (PDOException $error) {
            echo '<h1>Query error</h1><p>'.$error->getMessage().'</p><pre>'.$sql.((is_array($params))?"\n\nParams ".print_r($params, true):'').'</pre>';
            throw new PDOException('Query failed. '.$error->getMessage());
        }
    }

    # ===============================
    # Insert query, prepared statement
    # ===============================
    public function insert($table, $data)
    {
        # Check if data is array
        if (!is_array($data)) {
            throw new Exception('Data in insert is not an array.');
            return;
        }

        # preparing keys
        foreach (array_keys($data) as $arraykey => $key) {
            $keys[$arraykey] = '`'.$key.'`';
        }
        $keys = implode(',
            ', $keys);

        # Preparing quetionmarks
        $questionmarks = implode(', ', array_fill(0, count($data), '?'));

        # Create query
        $sql = '
        INSERT INTO `'.$table.'` (
            '.$keys.'
        )
        VALUES(
            '.$questionmarks.'
        )';

        # Fire query and return
        return $this->query($sql, array_values($data));
    }

    # ===============================
    # Update query, prepared statement
    # ===============================
    public function update($table, $data, $where = null, $lastaddon = null)
    {
        # Check if data is array
        if(!is_array($data)){
            throw new Exception('Data in update is not an array.');
            return;
        }

        # peparing update values
        $set = array();
        $escapes = array_values($data);
        foreach(array_keys($data) as $key)
            $set[] = '`'.$key.'` = ?';
        $set = implode(',
            ', $set);

        # Create query
        $sql = '
        UPDATE `'.$table.'` SET
            '.$set;

        # Adding where clause
        if(!empty($where)){
            if(is_array($where)){
                # if array escape values
                $first = true;
                foreach($where as $key => $value){
                    $operator = substr($key, -1);
                    if(in_array($operator, array('=', '!', '>', '<', '%'))){
                        if($operator == '!') $operator = '!=';
                        if($operator == '%') $operator = 'LIKE';
                        $key = substr($key, 0, -1);
                    }
                    else $operator = '=';
                    $sql .= '
        '.(($first)?'WHERE':'AND').' `'.$key.'` '.$operator.' ?';
                    $escapes[] = $value;
                    $first = false;
                }
            }
            # Where was no array, should be escaped with $this->quote();
            else $sql .= '
        '.$where;
        }

        # Checking for last addon (like limit 1)
        if(!empty($lastaddon)) $sql .= '
        '.$lastaddon;

        # Fire query and return
        return $this->query($sql, $escapes);
    }

    # ===============================
    # Delete query
    # ===============================
    public function delete($table, $where = null)
    {
        # Create query
        $sql = '
        DELETE FROM `'.$table.'`';

        # Adding where clause
        if(!empty($where)){
            if(is_array($where)){
                # if array escape values
                $first = true;
                $escapes = array();
                foreach($where as $key => $value){
                    $operator = substr($key, -1);
                    if(in_array($operator, array('=', '!', '>', '<', '%'))){
                        if($operator == '!') $operator = '!=';
                        if($operator == '%') $operator = 'LIKE';
                        $key = substr($key, 0, -1);
                    }
                    else $operator = '=';
                    $sql .= '
        '.(($first)?'WHERE':'AND').' `'.$key.'` '.$operator.' ?';
                    $escapes[] = $value;
                    $first = false;
                }
                # Execute delete with escapes
                return $this->query($sql, $escapes);
            }
            # Where was no array, should be escaped with $this->quote();
            else $sql .= '
        WHERE '.$where;
        }

        # Execute delete
        return $this->query($sql);
    }
}