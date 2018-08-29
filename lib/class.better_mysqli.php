<?php
class better_mysqli extends mysqli {
 
        // version  ::    date    :: comment
        // 1.1      :: 05/11/2010 :: Added 'delete' method
 
        private $prepared_statements = array();
    private $errstr = '';
 
 
    function errstr(){
        return $this->errstr;
    }
 
        function bind_placeholder_vars(&$stmt,$params,$debug=0) {
                // Credit to: Dave Morgan
                // Code ripped from: http://www.devmorgan.com/blog/2009/03/27/dydl-part-3-dynamic-binding-with-mysqli-php/
                if ($params != null) {
                        $types = '';                        //initial sting with types
                        foreach($params as $param) {        //for each element, determine type and add
                                if(is_int($param)) {
                                        $types .= 'i';              //integer
                                } elseif (is_float($param)) {
                                        $types .= 'd';              //double
                                } elseif (is_string($param)) {
                                        $types .= 's';              //string
                                } else {
                                        $types .= 'b';              //blob and unknown
                                }
                        }
 
                        $bind_names = array();
                        $bind_names[] = $types;             //first param needed is the type string
                                                                        // eg:  'issss'
 
                        for ($i=0; $i<count($params);$i++) {//go through incoming params and added em to array
                                $bind_name = 'bind' . $i;       //give them an arbitrary name
                                $$bind_name = $params[$i];      //add the parameter to the variable variable
                                $bind_names[] = &$$bind_name;   //now associate the variable as an element in an array
                        }
 
                        if($debug){
                                echo "\$bind_names:<br />\n";
                                var_dump($bind_names);
                                echo "<br />\n";
                        }
                        //error_log("better_mysqli has params ".print_r($bind_names, 1));
                        //call the function bind_param with dynamic params
                        call_user_func_array(array($stmt,'bind_param'),$bind_names);
                        return true;
                }else{
                        return false;
                }
        }
 
 
 
 
        function bind_result_array($stmt, &$row){
                // Credit to: Dave Morgan
                // Code ripped from: http://www.devmorgan.com/blog/2009/03/27/dydl-part-3-dynamic-binding-with-mysqli-php/
                $meta = $stmt->result_metadata();
                while ($field = $meta->fetch_field()){
                        $params[] = &$row[$field->name];
                }
                call_user_func_array(array($stmt, 'bind_result'), $params);
                return true;
        }
 
 
 
 
        function shut($statement){
                $stmt_key = md5($statement);
                if(array_key_exists($stmt_key,$this->prepared_statements)){
                        $stmt = $this->prepared_statements[$stmt_key]['stmt'];
                        $stmt->close();
                        unset($this->prepared_statements[$stmt_key]);
                }
                return $stmt;
        }
 
 
 
 
 
        function insert($statement, $params='', $debug=0, &$dm='', &$id_of_new_record=''){
                // note: $params = array of values to use for any placeholders used in statement
        // $id_of_new_record = Will be assigned the ID of the record just inserted. Obtained via $mysqli->insert_id
 
        $this->clear_sth();
 
        // Prepare the statement, if we haven't already
                $stmt = '';
                $stmt_key = md5($statement);
 
                if($debug){
                        $dm .= "\$stmt_key: $stmt_key<br />\nStatement:<br />\n<blockquote>$statement</blockquote>\n";
                        $dm .= "\$this->prepared_statements:<br />\n<pre>\n";
                        $dm .= print_r($this->prepared_statements, true);
                        $dm .= "</pre><br />\n";
                }
 
                if($statement==''){
                        if($debug){$dm .= "\$statement argument is blank!<br />\n";}
                        return false;
                }
 
                if(array_key_exists($stmt_key,$this->prepared_statements)){
                        if($debug){$dm .= "Using prepared statement<br />\n";}
                        $stmt = $this->prepared_statements[$stmt_key]['stmt'];
                        $stmt->reset();
                }else{
                        if($debug){$dm .= "Preparing insert statement <blockquote>$statement</blockquote> <br />\n";}
                        $stmt=$this->stmt_init();
                        if ($stmt->prepare($statement)) {
                                if($debug){$dm .= "Statement prepared OK<br />\n";}
                                $this->prepared_statements[$stmt_key]=array();
                                $this->prepared_statements[$stmt_key]['stmt'] = $stmt;
                                $this->prepared_statements[$stmt_key]['params_required'] = $stmt->param_count;
                                $this->prepared_statements[$stmt_key]['statement'] = $statement;
                        }else{
                                if($debug){$dm .= "ERROR preparing statement: ". $stmt->error .", ".$this->error."<br />\n";}
                                return false;
                   }
 
                }
                // we now have a prepared '$stmt' object
 
                // bind any placeholders, if required
                if($this->prepared_statements[$stmt_key]['params_required']){
                        if($params==''){
                                // TO-DO:  figure out how to set mysqli error and error number
                                if($debug){$dm .= "Statement requires 'params' array of values but none were given<br />\n";}
                                return false;
                        }
                        if(!$this->bind_placeholder_vars($stmt,$params,$debug)){
                                if($debug){$dm .= "Unable to 'bind_placeholder_vars'<br />\n";}
                                return false;
                        }
                }
                // our values have been bound to the placeholders
 
 
                // execute the statement
                if(!$stmt->execute()){
                        if($debug){$dm .= "ERROR executing statement: ". $stmt->error .", ".$this->error."<br />\n";}
                        return false;
                }
 
                $id_of_new_record = $this->insert_id;
 
                if($debug){$dm .= "All done in here,  affected rows were: ". $stmt->affected_rows .", new record id is: ". $id_of_new_record ."<br />\n";}
                return $stmt->affected_rows;
 
        }
 
 
 
        function clear_sth(){
                // clears any remaining results when using multi_query
                while($this->more_results()){
                        if($this->next_result()){
                                $result = $this->use_result();
                                $result->free_result();
                        }
                }
                // reset all exisitng prepared statements
                foreach($this->prepared_statements as $stmt_key => $a) {
                        $stmt = $this->prepared_statements[$stmt_key]['stmt'];
                        $stmt->reset();
                }
                return true;
        }
 
 
 
 
        function select($statement, &$row, $params='', $debug=0, &$dm=''){
 
        $row = array();
 
        $this->errstr = '';
 
                // Prepare the statement, if we haven't already
                $stmt = '';
                $stmt_key = md5($statement);
 
        if($debug > 1){
            $dm .= "\$stmt_key: $stmt_key<br />\nStatement:<br />\n<blockquote>$statement</blockquote>\n";
            $dm .= "\$this->prepared_statements:<br />\n<pre>\n";
            $dm .= print_r($this->prepared_statements, true);
            $dm .= "</pre><br />\n";
        }
 
        if($statement==''){
            if($debug){$dm .= "\$statement argument is blank!<br />\n";}
            $this->errstr = "\$statement argument is blank!";
            return false;
        }
 
                //cleanup any previous result sets that are still hanging out
                $this->clear_sth();
 
                if(array_key_exists($stmt_key, $this->prepared_statements)){
                        if($debug){$dm .= "Using prepared statement<br />\n";}
            $stmt = $this->prepared_statements[$stmt_key]['stmt'];
                }else{
                        if($debug){$dm .= "Preparing select statement <blockquote>$statement</blockquote> <br />\n";}
            $stmt=$this->stmt_init();
                        if ($stmt->prepare($statement)) {
                if($debug){$dm .= "Statement prepared OK<br />\n";}
                                $this->prepared_statements[$stmt_key]=array();
                                $this->prepared_statements[$stmt_key]['stmt'] = $stmt;
                                $this->prepared_statements[$stmt_key]['statement'] = $statement;
                                $this->prepared_statements[$stmt_key]['params_required'] = $stmt->param_count;
                        }else{
                                if($debug){$dm .= "ERROR preparing statement: ". $stmt->error .", ".$this->error."<br />\n";}
                $this->errstr = "ERROR preparing statement: ". $stmt->error .", ".$this->error;
                                return false;
                   }
                }
                // bind any placeholders, if required
                if($this->prepared_statements[$stmt_key]['params_required']){
                        if($params==''){
                                // TO-DO:  figure out how to set mysqli error and error number
                if($debug){$dm .= "Statement requires 'params' array of values but none were given<br />\n";}
                $this->errstr = "Statement requires 'params' array of values but none were given";
                return false;
                        }
                        if(!$this->bind_placeholder_vars($stmt,$params)){
                                if($debug){$dm .= "Unable to 'bind_placeholder_vars'<br />\n";}
                $this->errstr = "Unable to 'bind_placeholder_vars'";
                return false;
                        }
                }
                // execute the statement
                if(!$stmt->execute()){
                        if($debug){$dm .= "ERROR executing statement: ". $stmt->error .", ".$this->error."<br />\n";}
            $this->errstr = "ERROR executing statement: ". $stmt->error .", ".$this->error;
            return false;
                }
                // bind the results
                if(!$this->bind_result_array($stmt, $row)){
                        return false;
                }
                //return the stmt handle
        if($debug){$dm .= "All done in here,  affected rows were: ". $stmt->affected_rows ."<br />\n";}
                return $stmt;
        }
 
 
        function update($statement, $params='', $debug=0, &$dm=''){
                // note: $params = array of values to use for any placeholders used in statement
 
                // Prepare the statement, if we haven't already
                $stmt = '';
                $stmt_key = md5($statement);
 
                if($debug){
                        $dm .= "\$stmt_key: $stmt_key<br />\nStatement:<br />\n<blockquote>$statement</blockquote>\n";
                        $dm .= "\$this->prepared_statements:<br />\n<pre>\n";
                        $dm .= print_r($this->prepared_statements, true);
                        $dm .= "</pre><br />\n";
                }
 
                if($statement==''){
                        if($debug){$dm .= "\$statement argument is blank!<br />\n";}
                        return false;
                }
 
        //cleanup any previous result sets that are still hanging out
        $this->clear_sth();
 
                if(array_key_exists($stmt_key,$this->prepared_statements)){
                        if($debug){$dm .= "Using prepared statement<br />\n";}
                        $stmt = $this->prepared_statements[$stmt_key]['stmt'];
                        $stmt->reset();
                }else{
                        if($debug){$dm .= "Preparing update statement <blockquote>$statement</blockquote> <br />\n";}
                        $stmt=$this->stmt_init();
                        if ($stmt->prepare($statement)) {
                                if($debug){$dm .= "Statement prepared OK<br />\n";}
                                $this->prepared_statements[$stmt_key]=array();
                                $this->prepared_statements[$stmt_key]['stmt'] = $stmt;
                                $this->prepared_statements[$stmt_key]['params_required'] = $stmt->param_count;
                                $this->prepared_statements[$stmt_key]['statement'] = $statement;
                        }else{
                                if($debug){$dm .= "ERROR preparing statement: ". $stmt->error .", ".$this->error."<br />\n";}
                                return false;
                   }
 
                }
                // we now have a prepared '$stmt' object
 
                // bind any placeholders, if required
                if($this->prepared_statements[$stmt_key]['params_required']){
                        if($params==''){
                                // TO-DO:  figure out how to set mysqli error and error number
                                if($debug){$dm .= "Statement requires 'params' array of values but none were given<br />\n";}
                                return false;
                        }
                        if(!$this->bind_placeholder_vars($stmt,$params,$debug)){
                                if($debug){$dm .= "Unable to 'bind_placeholder_vars'<br />\n";}
                                return false;
                        }
                }
                // our values have been bound to the placeholders
 
 
                // execute the statement
                if(!$stmt->execute()){
                        if($debug){$dm .= "ERROR executing statement: ". $stmt->error .", ".$this->error."<br />\n";}
                        return false;
                }
 
                if($debug){$dm .= "All done in here,  affected rows were: ". $stmt->affected_rows ."<br />\n";}
                return $stmt->affected_rows>0?$stmt->affected_rows:true;
 
        }
 
 
        function delete($statement, $params='', $debug=0, &$dm=''){
 
                // Prepare the statement, if we haven't already
                $stmt = '';
                $stmt_key = md5($statement);
 
        if($debug){
            $dm .= "\$stmt_key: $stmt_key<br />\nStatement:<br />\n<blockquote>$statement</blockquote>\n";
            $dm .= "\$this->prepared_statements:<br />\n<pre>\n";
            $dm .= print_r($this->prepared_statements, true);
            $dm .= "</pre><br />\n";
        }
 
        //cleanup any previous result sets that are still hanging out
        $this->clear_sth();
 
                if(array_key_exists($stmt_key, $this->prepared_statements)){
            if($debug){$dm .= "Using prepared statement<br />\n";}
                        $stmt = $this->prepared_statements[$stmt_key]['stmt'];
                        $stmt->reset();
                }else{
            if($debug){$dm .= "Preparing update statement <blockquote>$statement</blockquote> <br />\n";}
                        //cleanup any previous result sets that are still hanging out
                        $this->clear_sth();
                        $stmt=$this->stmt_init();
                        if ($stmt->prepare($statement)) {
                                $this->prepared_statements[$stmt_key]=array();
                                $this->prepared_statements[$stmt_key]['stmt'] = $stmt;
                                $this->prepared_statements[$stmt_key]['statement'] = $statement;
                                $this->prepared_statements[$stmt_key]['params_required'] = $stmt->param_count;
                        }else{
                                # delete
                                if($debug){$dm .= "ERROR preparing statement: ". $stmt->error .", ".$this->error."<br />\n";}
                                return false;
                   }
                }
                // bind any placeholders, if required
                if($this->prepared_statements[$stmt_key]['params_required']){
                        if($params==''){
                                // TO-DO:  figure out how to set mysqli error and error number
                if($debug){$dm .= "Statement requires 'params' array of values but none were given<br />\n";}
                                return false;
                        }
                        if(!$this->bind_placeholder_vars($stmt,$params)){
                                if($debug){$dm .= "Unable to 'bind_placeholder_vars'<br />\n";}
                return false;
                        }
                }
                // execute the statement
                if(!$stmt->execute()){
                        if($debug){$dm .= "ERROR executing statement: ". $stmt->error .", ".$this->error."<br />\n";}
            return false;
                }
        if($debug){$dm .= "All done in here,  affected rows were: ". $stmt->affected_rows ."<br />\n";}
                return true;
        }
 
 
 
 
        private function requires_placeholder_params($statement){
                if(preg_match('/\?/',$statement)){
                        return true;
                }else{
                        return false;
                }
        }
 
 
 
} // end class
?>