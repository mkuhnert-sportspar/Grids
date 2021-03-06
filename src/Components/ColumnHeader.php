<?php

namespace SportSpar\Grids\Components;

use SportSpar\Grids\FieldConfig;

/**
 * Class ColumnHeader
 *
 * The component for rendering column header
 */
class ColumnHeader extends TableCell
{
    protected $tag_name = 'th';

    /**
     * @param FieldConfig $column
     *
     * @return $this
     */
    public function setColumn(FieldConfig $column)
    {
        $this->setContent($column->getLabel());
        if ($column->isSortable()) {
            $this->addComponent(new SortingControl($column));
        }

        return parent::setColumn($column);
    }
}
