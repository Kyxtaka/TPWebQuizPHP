<?php
interface Evaluable {
    /**
     * Calcule le score de la question
     * @param string|array $answer La réponse à la question
     * @return int Le score de la question
     */
    public function calculeScore(string | array $answer);
}
?>