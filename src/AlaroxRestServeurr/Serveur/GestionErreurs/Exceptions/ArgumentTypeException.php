<?php
namespace AlaroxRestServeur\Serveur\GestionErreurs\Exceptions;

class ArgumentTypeException extends MainException
{
    /**
     * @var string
     */
    private $_obtenu;

    /**
     * @param int $codeStatus
     * @param string $methode
     * @param string $attendu
     * @param mixed $typeVariable
     */
    public function __construct($codeStatus, $attendu, $typeVariable)
    {
        $trace = debug_backtrace();
        $caller = array_shift($trace);

        if (!is_object($typeVariable)) {
            $this->setObtenu(gettype($typeVariable));
        } else {
            $this->setObtenu($this->_obtenu = get_class($typeVariable));
        }

        parent::__construct(
            1000, $codeStatus, $caller['file'] . '::' . $caller['object']->getTrace()[0]['function'] . '()', $attendu,
            $this->_obtenu
        );
    }

    /**
     * @param string $typeObtenu
     */
    public function setObtenu($typeObtenu)
    {
        $this->_obtenu = $typeObtenu;
    }
}