<?php
/**
 * Zoner form validation
 */

class form_validation
{

    /*Massive of errors*/
    private $errors = array();

    /*Container of rules for forms by name*/
    private function fields($formName)
	{
        switch ($formName) {
            case 'property':
                return array(
                    'title' => array('req'),
                    'price' => array('req', 'number'),
                    'address' => array('req'),
                    'city' => array('req'),
                    'area' => array('req')
                );
                break;

            case 'agency':
                return array(
                    'agency-title' => array('req'),
                    'agency-aboutus' => array('req'),
                    'agency-address' => array('req'),
                    'agency-email' => array('req', 'email')
                );
                break;
            default:
                return array();
                break;
        }
    }

    private function check_req($value)
    {
        //delete whitespaces for check
        if (preg_replace('/\s+/', '', $value) == '') {
            return true;
        } else {
            return false;
        }
    }

    private function check_number($value)
    {
        //it must be number
        //if empty = then return false(number it's not required)
        if ($this->check_req($value))
            return false;

        if (!is_numeric($value)) {
            return true;
        } else {
            return false;
        }
    }

    private function check_email($value)
    {
        //it must be example@domain.com
        //it can be empty
        if ($this->check_req($value))
            return false;

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    //function that check form by form name @function fields
    public function check($formName, $formType = 'post')
    {
        $fields = $this->fields($formName);

        foreach ($fields as $key => $field) {
            //get form data
            if ($formType == 'post' && isset($_POST[$key]))
                $field_data = $_POST[$key];
            if ($formType == 'get'  && isset($_GET[$key]))
                $field_data = $_GET[$key];

            if (isset($field_data)) {
                //Check for only number fields
                if (in_array('number', $field) && $this->check_number($field_data)) {
                    $this->errors[$key]['number'] = $field_data;
                }
                //Check for true email
                if (in_array('email', $field) && $this->check_email($field_data)) {
                    $this->errors[$key]['email'] = $field_data;
                }
                //Check for empty fields
                if (in_array('req', $field) && $this->check_req($field_data)) {
                    $this->errors[$key]['req'] = $field_data;
                }
            }

        }

        if (empty($this->errors))
            return true;
        else
            return false;
    }

    //get all errors that we have
    //and clear them for next check
    public function getErrors()
    {
        $return_errors = $this->errors;
        $this->errors = array(); //clear errors
        return $return_errors;
    }

    public function listErrors()
    {
        $result='';
        $errors = $this->getErrors();
        $result.='<ul>';
        foreach ($errors as $key => $error) {
            $result.= '<li>' . $key . '</li>';
        }
        $result.='</ul>';
        return $result;
    }
}