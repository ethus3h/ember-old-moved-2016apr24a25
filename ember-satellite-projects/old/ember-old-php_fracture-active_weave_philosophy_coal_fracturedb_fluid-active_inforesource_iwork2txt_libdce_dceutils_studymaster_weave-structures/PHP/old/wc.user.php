<?php
class user
{
    var $id;
    var $registration_ip;
    var $node_id;
    var $name;
    var $authorisation_type;
    var $password_md5;
    var $ip_list;
    var $node_edit_ids;
    function __construct($id, $registration_ip, $node_id, $name, $authorisation_type, $password_md5, $ip_list, $node_edit_ids)
    {
        $this->id = $id;
        $this->registration_ip = $registration_ip;
        $this->node_id = $node_id;
        $this->name = $name;
        $this->authorisation_type = $authorisation_type;
        $this->password_md5 = $password_md5;
        $this->ip_list = $ip_list;
        $this->node_edit_ids = $node_edit_ids;
    }
    function set_variable($variable, $new_content)
    {
        $this->$variable = $new_content;
        $query='INSERT INTO `user` (`user_id`, `user_registration_ip`, `user_node_id`, `user_name`, `user_authorisation_type`, `user_password_md5`, `user_ip_list`, `user_node_edit_ids`) VALUES (' . $this->id . ', \'' . $this->registration_ip . '\', \'' . $this->node_id . '\', \'' . $this->name . '\', \'' . '1' . '\', \'' . $this->authorisation_type . '\', \'' . $this->password_md5 . '\', \'' . $this->ip_list . '\', ' . $this->node_edit_ids . ');';
        mysql_query($query);
    }
    function get_content($variable)
    {
        return $this->$variable;
    }
    function request_content($filter, $filtervalue)
    {
        $this->id = qry('user', 'user_id', $filter, $filtervalue);
        $this->registration_ip = qry('user', 'user_registration_ip', $filter, $filtervalue);
        $this->node_id = qry('user', 'user_node_id', $filter, $filtervalue);
        $this->name = qry('user', 'user_name', $filter, $filtervalue);
        $this->authorisation_type = qry('user', 'user_authorisation_type', $filter, $filtervalue);
        $this->password_md5 = qry('user', 'user_password_md5', $filter, $filtervalue);
        $this->ip_list = qry('user', 'user_ip_list', $filter, $filtervalue);
        $this->node_edit_ids = qry('user', 'user_node_edit_ids', $filter, $filtervalue);
    }
}
?>
