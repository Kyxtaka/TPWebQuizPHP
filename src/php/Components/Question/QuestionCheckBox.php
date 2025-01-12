<?php
namespace Components\Question;
use Components\Form\TextField;
use Components\Form\Checkbox;
use Components\Form\Radiobox;
use \Exception;

class QuestionCheckBox extends Question{
    public function render() {
        $html = '<div class="question">';
        $html .= '<label>' . $this->label . '</label>';
        foreach ($this->getChoices() as $choice) {
            $input  = new Checkbox($this->getUuid(), $choice);
            $html .= $input->render();
        }
        $html .= '</div>';
        return $html;
    }
}
?>