<?php
namespace Components\Question;
use Components\Question\Question;
use Components\Form\TextField;
use Components\Form\Checkbox;
use Components\Form\Radiobox;
use \Exception;

class QuestionTextField extends Question{    
    public function render() {
        $html = '<div class="question">';
        $input  = new TextField($this->getUuid(), $this->getLabel() . " :");
        $html .= $input->render();
        $html .= '</div>';
        return $html;
    }
}
?>