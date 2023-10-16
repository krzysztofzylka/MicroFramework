<?php

namespace Krzysztofzylka\MicroFramework\Extension\Table\Trait;

use Krzysztofzylka\MicroFramework\Extension\Table\Extra\Cell;
use krzysztofzylka\SimpleLibraries\Library\_Array;
use krzysztofzylka\SimpleLibraries\Library\Strings;

/**
 * Renders
 * @package Extension\Table\Trait
 */
trait Render
{

    /**
     * Generowanie stopki
     * @return void
     */
    public function renderFooter(): void
    {
        $this->html .= '<div class="footer float-end">';

        if ($this->activePagination) {
            $this->html .= '<form method="POST">';
            $this->html .= '<input type="hidden" name="table_id" value="' . $this->id . '" />';
            $this->html .= '<nav aria-label="navigation"><ul class="pagination">';
            $this->html .= '<li class="page-item ' . (in_array($this->page, [0, 1]) ? 'disabled' : '') . '"><input type="submit" name="page" class="page-link" value="&#171;&#171;" /></li>';
            $this->html .= '<li class="page-item ' . (in_array($this->page, [0, 1]) ? 'disabled' : '') . '"><input type="submit" name="page" class="page-link" value="&#171;" /></li>';
            $from = $this->page - 3;

            for ($i = 0; $i < 7; $i++) {
                if ($from + $i < 1 || $from + $i > $this->pages) {
                    continue;
                }

                $this->html .= '<li class="page-item' . ($this->page === $from + $i ? ' active' : '') . '"><input type="submit" name="page" class="page-link" value="' . ($from + $i) . '" /></li>';
            }

            if ((int)$this->pages === 0) {
                $this->html .= '<li class="page-item active"><input type="submit" name="page" class="page-link" value="1" /></li>';
            }

            $this->html .= '<li class="page-item ' . (($this->pages === 0 || $this->pages === $this->page || $this->pages === $this->page - 1) ? 'disabled' : '') . '"><input type="submit" name="page" class="page-link" value="&#187;" /></li>';
            $this->html .= '<li class="page-item ' . (($this->pages === 0 || $this->pages === $this->page || $this->pages === $this->page - 1) ? 'disabled' : '') . '"><input type="submit" name="page" class="page-link" value="&#187;&#187;" /></li>';
            $this->html .= '</ul></nav>';
            $this->html .= '</form>';
        }

        $this->html .= '</div>';
    }

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
                $style = [];
                $cell = new Cell();
                $cell->val = _Array::getFromArrayUsingString($columnKey, $result);
                $cell->data = $result;
                $wordBreak = $column['wordBreak'] ?? false;
                $noWrap = $column['noWrap'] ?? false;

                if (isset($column['width'])) {
                    $style[] = 'width:' . (int)$column['width'] . 'px';
                }

                if ($wordBreak) {
                    $style[] = 'word-break: break-all';
                }

                if ($noWrap) {
                    $style[] = 'white-space: nowrap';
                }

                $this->html .= '<td style="' . implode('; ', $style) . '">';

                if (isset($column['value']) && is_string($column['value'])) {
                    $value = $column['value'];
                } elseif (isset($column['value']) && is_object($column['value'])) {
                    $value = $column['value']($cell);
                } else {
                    $value = $cell->val;
                }

                if (isset($column['maxChar']) && is_int($column['maxChar'])) {
                    if (mb_strlen($value) > $column['maxChar']) {
                        $value = mb_strimwidth(
                            Strings::removeLineBreaks($value),
                            0,
                            $column['maxChar'],
                            '...',
                            'UTF-8'
                        );
                    }
                }

                $this->html .= $value;

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
        $this->html .= '<div class="actions">';
        $this->html .= '<div class="row">';
        $this->html .= '<div class="col">';

        foreach ($this->actions as $action) {
            $type = $action['type'] ?? 'primary';
            $value = $action['value'] ?? '';
            $href = $action['href'] ?? '#';
            $class = $action['class'] ?? '';
            $dialogbox = $action['dialogbox'] ?? true;

            if ($dialogbox) {
                $class .= ' ajaxlink';
            }

            $this->html .= '<a class="btn btn-' . $type . ' me-2 ' . $class . '" href="' . $href . '">' . $value . '</a>';
        }

        $this->html .= '</div>';
        $this->html .= '<div class="col-3 float-end">';
        if ($this->activeSearch) {
            $this->html .= '<form method="POST" class="float-end me-2"><input type="hidden" name="table_id" value="' . $this->id . '" /><input name="search" class="form-control" placeholder="' . __('micro-framework.table.search') . '" value="' . $this->search . '" /></form>';
        }

        if ($this->activePaginationLimit) {
            $this->html .= '<form method="POST" class="float-end me-2"><input type="hidden" name="table_id" value="' . $this->id . '" /><select class="form-select" name="paginationLimit" ' . ($this->isAjax ? '' : 'onchange="this.form.submit()"') . '>';

            foreach ($this->paginationLimits as $limit) {
                $this->html .= '<option value="' . $limit . '" ' . ($limit === $this->paginationLimit ? 'selected' : '') . '>' . $limit . '</option>';
            }

            $this->html .= '</select></form>';
        }

        $this->html .= '</div>';
        $this->html .= '</div>';
        $this->html .= '</div>';
    }

}