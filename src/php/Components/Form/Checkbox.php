<?php
namespace Components\Form;
class Checkbox {
    private $name;
    private $label;
    private $isChecked;

    /**
     * Constructeur
     *
     * @param string $name Le nom de l'élément checkbox (attribut HTML `name`)
     * @param string $label Le texte affiché à côté de la checkbox
     * @param bool $isChecked Indique si la checkbox est cochée par défaut
     */
    public function __construct(string $name, string $label, bool $isChecked = false) {
        $this->name = $name;
        $this->label = $label;
        $this->isChecked = $isChecked;
    }

    /**
     * Génère le HTML de la checkbox
     *
     * @return string Le code HTML de la checkbox
     */
    public function render(): string {
        $checked = $this->isChecked ? 'checked' : '';
        return sprintf(
            '<label><input type="checkbox" name="%s" %s value="%s"> %s</label>',
            htmlspecialchars($this->name),
            $checked,
            htmlspecialchars($this->label),
            htmlspecialchars($this->label)
        );
    }

    /**
     * Définir si la checkbox est cochée
     *
     * @param bool $isChecked
     */
    public function setChecked(bool $isChecked): void {
        $this->isChecked = $isChecked;
    }

    /**
     * Vérifie si la checkbox est cochée
     *
     * @return bool
     */
    public function isChecked(): bool {
        return $this->isChecked;
    }

    /**
     * Obtenir le nom de la checkbox
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Obtenir le texte du label
     *
     * @return string
     */
    public function getLabel(): string {
        return $this->label;
    }
}

?>
