<?php

class Smarty_Internal_Compile_Js extends Smarty_Internal_CompileBase
{

    public $option_flags = ['nocache', 'noscope'];

    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter)
    {
        $this->required_attributes = ['action'];
        $_attr = $this->getAttributes($compiler, $args);
        $action = str_replace(['"', "'", ' '], '', $_attr['action']);

        return "<script src='{$_ENV['URL']}/public_files/js/{$action}'></script>";
    }
}
