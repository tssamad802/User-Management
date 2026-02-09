<?php
class controller extends model
{
    public function is_empty_inputs($fields = [])
    {
        foreach ($fields as $field) {
            if (empty($field)) {
                return true;
            }
        }
        return false;
    }
    public function is_only_char($fields = [])
    {
        foreach ($fields as $field) {
            if (!preg_match("/^[a-zA-Z ]/", $field)) {
                return true;
            }
        }
        return false;
    }

}
?>