<?php

namespace Krzysztofzylka\MicroFramework\Extension\Table\Trait;

use Krzysztofzylka\MicroFramework\Extension\Table\Extra\Cell;

trait Render {

    /**
     * Render headers
     * @return void
     */
    private function renderHeaders(): void
    {
        $this->html .= '<thead><tr>';

        foreach ($this->columns as $column) {
            $this->html .= '<th>' . ($column['title'] ?? '') . '</th>';
        }

        $this->html .= '</tr></thead>';
    }

    /**
     * Render body
     * @return void
     */
    private function renderBody(): void
    {
        $this->html .= '<tbody>';

        foreach ($this->results as $result) {
            $this->html .= '<tr>';

            foreach ($this->columns as $columnKey => $column) {
                $cell = new Cell();
                $cell->val = $this->getArrayData($columnKey, $result);
                $cell->data = $result;
                $this->html .= '<td>';

                if (isset($column['value']) && is_string($column['value'])) {
                    $this->html .= $column['value'];
                } elseif (isset($column['value']) && is_object($column['value'])) {
                    $this->html .= $column['value']($cell);
                } else {
                    $this->html .= $cell->val;
                }

                $this->html .= '</td>';
            }

            $this->html .= '</tr>';
        }

        $this->html .= '</tbody>';
    }

    /**
     * Render actions
     * @return void
     */
    private function renderAction(): void
    {
        $this->html .= '<div class="actions float-end">';

        if ($this->activeSearch) {
            $this->html .= '<form method="POST"><input type="hidden" name="table_id" value="' . $this->id . '" /><input name="search" class="form-control" placeholder="Search..." value="' . $this->search . '" /></form>';
        }

        $this->html .= '</div>';
    }

    public function renderFooter(): void
    {
        $this->html .= '<div class="footer float-end">';
        $this->html .= '<form method="POST">';
        $this->html .= '<input type="hidden" name="table_id" value="' . $this->id . '" />';
        $this->html .= '<nav aria-label="navigation"><ul class="pagination">';
        $this->html .= '<li class="page-item"><input type="submit" name="page" class="page-link" value="&#171;" /></li>';
        $from = $this->page - 3;

        for ($i = 0; $i < 7; $i++) {
            if ($from + $i < 1 || $from + $i > $this->pages) {
                continue;
            }

            $this->html .= '<li class="page-item' . ($this->page === $from + $i ? ' active' : '') . '"><input type="submit" name="page" class="page-link" value="' . ($from + $i) . '" /></li>';
        }

        $this->html .= '<li class="page-item"><input type="submit" name="page" class="page-link" value="&#187;" /></li>';
        $this->html .= '</ul></nav>';
        $this->html .= '</form>';
        $this->html .= '</div>';
    }

}