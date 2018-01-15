<?php

namespace HTMLBuilder\Handler;

/**
 *  Gestione dell'input di tipo "text", "file", "password", "email", "number", "textarea" e "hidden".
 *
 * @since 2.3
 */
class DefaultHandler implements HandlerInterface
{
    public function handle(&$values, &$extras)
    {
        // Delega della gestione al metodo specifico per il tipo di input richiesto
        if (in_array($values['type'], get_class_methods($this))) {
            $result = $this->{$values['type']}($values, $extras);
        }

        // Caso non previsto
        else {
            $result = $this->custom($values, $extras);
        }

        return $result;
    }

    /**
     * Gestione dell'input di tipo non altrimenti previsto.
     * Esempio: {[ "type": "undefined", "label": "Custom di test", "placeholder": "Test", "name": "custom", "value": "custom" ]}.
     *
     * @param array $values
     * @param array $extras
     *
     * @return string
     */
    protected function custom(&$values, &$extras)
    {
        // Generazione del codice HTML
        return '
    <span |attr|>|value|</span>';
    }

    /**
     * Gestione dell'input di tipo "text".
     * Esempio: {[ "type": "text", "label": "Text di test", "placeholder": "Test", "name": "text" ]}.
     *
     * @param array $values
     * @param array $extras
     *
     * @return string
     */
    protected function text(&$values, &$extras)
    {
        // Generazione del codice HTML
        return '
    <input |attr|>';
    }

    /**
     * Gestione dell'input di tipo "file".
     * Esempio: {[ "type": "file", "label": "File di test", "placeholder": "Test", "name": "file" ]}.
     *
     * @param array $values
     * @param array $extras
     *
     * @return string
     */
    protected function file(&$values, &$extras)
    {
        // Delega al metodo "text", per la generazione del codice HTML
        return $this->text($values, $extras);
    }

    /**
     * Gestione dell'input di tipo "password".
     * Esempio: {[ "type": "password", "label": "Password di test", "placeholder": "Test", "name": "password" ]}.
     *
     * @param array $values
     * @param array $extras
     *
     * @return string
     */
    protected function password(&$values, &$extras)
    {
        // Delega al metodo "text", per la generazione del codice HTML
        return $this->text($values, $extras);
    }

    /**
     * Gestione dell'input di tipo "hidden".
     * Esempio: {[ "type": "hidden", "label": "Hidden di test", "placeholder": "Test", "name": "hidden" ]}.
     *
     * @param array $values
     * @param array $extras
     *
     * @return string
     */
    protected function hidden(&$values, &$extras)
    {
        $original = $values;

        $values = [];
        $values['type'] = $original['type'];
        $values['value'] = $original['value'];
        $values['name'] = $original['name'];
        $values['class'] = [];

        // Delega al metodo "text", per la generazione del codice HTML
        return $this->text($values, $extras);
    }

    /**
     * Gestione dell'input di tipo "email".
     * Esempio: {[ "type": "email", "label": "Email di test", "placeholder": "Test", "name": "email" ]}.
     *
     * @param array $values
     * @param array $extras
     *
     * @return string
     */
    protected function email(&$values, &$extras)
    {
        $values['class'][] = 'email-mask';

        $values['type'] = 'text';

        // Delega al metodo "text", per la generazione del codice HTML
        return $this->text($values, $extras);
    }

    /**
     * Gestione dell'input di tipo "number".
     * Esempio: {[ "type": "number", "label": "Number di test", "placeholder": "Test", "name": "number" ]}.
     *
     * @param array $values
     * @param array $extras
     *
     * @return string
     */
    protected function number(&$values, &$extras)
    {
        $values['class'][] = 'inputmask-decimal';

        $values['value'] = !empty($values['value']) ? $values['value'] : 0;

        // Gestione della precisione (numero specifico, oppure "qta" per il valore previsto nell'impostazione "Cifre decimali per quantità").
        $decimals = null;
        if (isset($values['decimals'])) {
            if (is_numeric($values['decimals'])) {
                $decimals = $values['decimals'];
            } elseif (starts_with($values['decimals'], 'qta')) {
                // Se non è previsto un valore minimo, lo imposta a 1
                $values['min-value'] = isset($values['min-value']) ? $values['min-value'] : 1;

                $decimals = \Settings::get('Cifre decimali per quantità');
                $values['decimals'] = $decimals;
            }
        }

        // Controllo sulla correttezza sintattica del valore impostato
        $values['value'] = (\Translator::getFormatter()->isStandardNumber($values['value'])) ? \Translator::numberToLocale($values['value'], $decimals) : $values['value'];

        $values['type'] = 'text';

        // Delega al metodo "text", per la generazione del codice HTML
        return $this->text($values, $extras);
    }

    /**
     * Gestione dell'input di tipo "textarea".
     * Esempio: {[ "type": "textarea", "label": "Textarea di test", "placeholder": "Test", "name": "textarea" ]}.
     *
     * @param array $values
     * @param array $extras
     *
     * @return string
     */
    protected function textarea(&$values, &$extras)
    {
        $values['class'][] = 'autosize';

        // Generazione del codice HTML
        return '
    <textarea |attr|>|value|</textarea>';
    }
}
