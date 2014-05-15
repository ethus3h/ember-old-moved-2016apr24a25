<?php
class text
{
    var $content;
    var $reference;
    var $caption;
    //$content is self-explanatory. $reference is the string used to reference this in building HTML forms (e. g. nodeTitle). $caption is the label to use when building HTML forms.
    function __construct($content, $reference, $caption)
    {
        $this->content = $content;
        $this->reference = $reference;
        $this->caption = $caption;
    }
    function set_content($new_content)
    {
        $this->content = $new_content;
    }
    function get_content()
    {
        return $this->content;
    }
    function request_content($request)
    {
        return $this->caption . ': <input type="text" name="' . $this->reference . '"><br>';
    }
    function request_new($request, $original_content)
    {
        return $this->caption . ': <input type="text" name="' . $this->reference . '" value="' . $this->content . '><br>';
    }
}
?>
