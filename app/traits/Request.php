<?php

namespace app\traits;

use core\Csrf;

trait Request {

    private $validation_errors = [];

    public function post($index) {
        return $_POST[$index] ?? null;
    }

    public function file($index) {
        return $_FILES[$index] ?? null;
    }

    public function validate($fields) {
        foreach($fields as $field_name => $rules) {
            $field_value = $this->post($field_name);

            if($field_value == null) {
                $field_value = $this->file($field_name);
                if($field_value == null) {
                    continue;
                }
            }

            $rules = explode('|', $rules);
            foreach($rules as $rule) {
                $rule = explode('=', $rule);
                switch($rule[0]) {
                    case 'required':
                        if(!isset($field_value) || (is_string($field_value) and strlen($field_value) == 0)) {
                            array_push($this->validation_errors, "Campo '{$field_name}' é necessário.");
                        }
                        break;

                    case 'max':
                        if(strlen($field_value) > $rule[1]) {
                            array_push($this->validation_errors, "Campo '{$field_name}' aceita no máximo '{$rule[1]}' caracteres.");
                        }
                        break;

                    case 'min':
                        if (strlen($field_value) < $rule[1]) {
                            array_push($this->validation_errors, "Campo '{$field_name}' aceita no mínimo '{$rule[1]}' caracteres.");
                        }
                        break;

                    case 'numeric':
                        !is_numeric($field_value) ? array_push($this->validation_errors, "Campo '{$field_name}' deve ser numérico.") : null;
                        break;

                    case 'string':
                        !is_string($field_value) ? array_push($this->validation_errors, "Campo '{$field_name}' deve ser string.") : null;
                        break;
                    
                    case 'file':
                        if (!isset($field_value) || $field_value['error'] != UPLOAD_ERR_OK) {
                            array_push($this->validation_errors, "Campo '{$field_name}' deve ser um arquivo.");
                        }
                        break;
                }
            }
        }

        return empty($this->validation_errors) ? false : $this->validation_errors;
    }

    public function verify_csrf() {
        Csrf::verify_csrf($this->post('token'));
    }
}