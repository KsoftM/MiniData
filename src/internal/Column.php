<?php

namespace ksoftm\system\internal;

use ksoftm\system\utils\datatype\Dictionary;

class Column
{
    public Dictionary $argument;
    /**
     * class construct
     *
     * @param string $name name of the column
     * @param string $type datatype of the column without any parameter arguments
     * @param array $args must be associative array
     */
    public function __construct(string $name, string $type)
    {
        $this->argument = new Dictionary();
        $this->name($name)->datatype($type);
    }

    private function name(string $name): Column
    {
        if (empty($name)) return $this;

        $this->argument->add('name', $name);
        return $this;
    }

    private function datatype(string $type): Column
    {
        if (empty($type)) return $this;

        $this->argument->add('datatype', $type);
        return $this;
    }

    public function unique(): Column
    {
        $this->argument->add('unique', 'unique');
        return $this;
    }

    public function unsigned(): Column
    {
        $this->argument->add('unsigned', 'unsigned');
        return $this;
    }

    public function nullable(): Column
    {
        $this->argument->add('nullable', 'null');
        return $this;
    }

    public function autoIncrement(): Column
    {
        $this->argument->add('autoIncrement', 'AUTO_INCREMENT');
        return $this;
    }

    public function default(string $default, bool $wrapped = true): Column
    {
        if (empty($default)) return $this;

        $this->argument->add('default', $wrapped ? "'$default'" : $default);
        return $this;
    }

    public function primaryKey(): Column
    {
        $this->argument->add('primaryKey', $this->argument->getValue('name'));
        return $this;
    }

    public function key(): Column
    {
        $this->argument->add('key', $this->argument->getValue('name'));
        return $this;
    }

    public function foreignKey(
        string $foreignKey,
        string $onUpdate = 'set null',
        string $onDelete = 'set null'
    ): Column {
        if (empty($foreignKey)) return $this;

        $this->unsigned();
        $this->nullable();
        $d = new Dictionary;
        $d->add('mainKey', $this->argument->getValue('name'));
        $d->add('foreignKey', $foreignKey);
        $d->add('onDelete', empty($onDelete) ? '' : "on Delete $onDelete");
        $d->add('onUpdate', empty($onUpdate) ? '' : "on Update $onUpdate");
        $this->argument->add('foreignKey', $d);
        return $this;
    }

    public function index(): ?Column
    {
        $this->argument->add('index', $this->argument->getValue('name'));
        return $this;
    }
}
