<?php
namespace PODataHeaven\CellFormatter;

use PODataHeaven\Exception\IdOfEntitiesNotSpecifiedException;

class IdOfEntitiesFormatter extends AbstractFormatter
{
    public function __construct(array $parameters = [])
    {
        parent::__construct();

        if (!isset($parameters['idOfEntities']) || !is_array($parameters['idOfEntities']) || [] === $parameters['idOfEntities']) {
            throw new IdOfEntitiesNotSpecifiedException;
        }
        $this->idOfEntities = $parameters['idOfEntities'];
        $this->sort();
    }

    public function format($value, array $row = [])
    {
        return sprintf(
            '<a href="/by-entity/%s/%s">%s</a>',
            join(',', $this->idOfEntities),
            $value,
            $this->escape($value)
        );
    }

    public function ensureEntityPresented($entityName)
    {
        if (!in_array($entityName, $this->idOfEntities, true)) {
            $this->idOfEntities[] = $entityName;
        }
        $this->sort();
    }

    protected function sort()
    {
        sort($this->idOfEntities);
    }
}
