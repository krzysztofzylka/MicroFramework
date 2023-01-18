<?php

namespace Krzysztofzylka\MicroFramework\Extension\Html;

use Exception;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Extension\Html\Trait\Form;

/**
 * Html helper
 * @package Biblioteki
 */
class Html {

    use Form;

    /**
     * Treść która zostanie zwrócona
     * @var string
     * @ignore
     */
    protected string $htmlString = '';

    /**
     * Zwracanie treści
     * @return string
     */
    public function __toString() {
        $htmlString = $this->htmlString;
        $this->htmlString = '';

        return $htmlString;
    }

    /**
     * Generowanie atrybutów tagów
     * @param array $attributes lista atrybutow np. ['id' => 'abc', 'class' => 'def']
     * @return string
     * @throws MicroFrameworkException
     */
    public static function generateAttributes(?array ...$attributes) : string {
        try {
            if (is_null($attributes[0])) {
                return '';
            }

            $attributesList = [];
            $attributesString = '';

            foreach ($attributes as $attribute) {
                foreach ($attribute as $key => $attr) {
                    if (isset($attributesList[$key]) and !in_array($key, ['id', 'type', 'value'])) {
                        $attributesList[$key] .= ' ' . $attr;
                    } else {
                        $attributesList[$key] = $attr;
                    }
                }
            }

            foreach ($attributesList as $key => $attribute) {
                if (str_contains($attributesString, '"')) {
                    $attributesString .= $key . '=\'' . $attribute . '\' ';
                } else {
                    $attributesString .= $key . '="' . $attribute . '" ';
                }
            }

            return ' ' . trim($attributesString);
        } catch (Exception) {
            throw new MicroFrameworkException('Wystąpił błąd podczas generowania atrybutów html.', 500, null);
        }
    }

    /**
     * Generowanie tag'a
     * @param string $name nazwa
     * @param ?string $content Zawartość
     * @param ?array $attributes Tablica z atrybutami np. ['id' => 'abc']
     * @return Html
     * @throws MicroFrameworkException
     */
    public function tag(string $name, ?string $content, ?array $attributes = null) : Html {
        $this->htmlString .= '<' . $name . self::generateAttributes($attributes)
            . (is_null($content) ? '/>' : ( '>' . $content . '</' . $name . '>'));

        return $this;
    }

    /**
     * Generowanie taba bez wzglęgu na aktualną instancję
     * @param string $name nazwa
     * @param ?string $content zawartość
     * @param ?array $attributes tablica z atrybutami np. ['id' => 'abc']
     * @return Html
     * @throws MicroFrameworkException
     */
    public function clearTag(string $name, ?string $content = null, ?array $attributes = null) : Html {
        return (new Html())->tag($name, $content, $attributes);
    }

    /**
     * Rows
     * @param string $data
     * @param array $attributes
     * @return $this
     * @throws MicroFrameworkException
     */
    public function row(string $data, array $attributes = []) : Html {
        $attributes = ['class' => 'row', ...$attributes ?? []];

        return $this->tag('div', $data, $attributes);
    }

    /**
     * Cols
     * @param ?string $data
     * @param ?array $attributes
     * @return $this
     * @throws MicroFrameworkException
     */
    public function col(?string $data, ?array $attributes = null) : Html {
        $attributes = ['class' => 'col', ...$attributes ?? []];

        return $this->tag('div', $data, $attributes);
    }

    /**
     * Col blocksy
     * @param string $data treść
     * @param ?string $href link
     * @param string $bg kolor tła
     * @param string $textColor kolor tekstu
     * @param bool $ajaxLink
     * @param array $attributes dodatkowe atrybuty ['col' => [atrybuty col], 'container' => [atrybuty div/a]]
     * @return $this
     * @throws MicroFrameworkException
     */
    public function colBlock(string $data, ?string $href = null, string $bg = 'primary', string $textColor = 'white', bool $ajaxLink = false, array $attributes = []) : Html {
        $attributes['container'] = ['class' => 'col text-decoration-none', ...$attributes['container'] ?? []];
        $attributes['col'] = ['class' => 'shadow p-2 mb-3 bg-' . $bg . ' text-' . $textColor . ' rounded text-black', ...$attributes['col'] ?? []];

        if ($href) {
            $attributes['container']['href'] = $href;
        }

        if ($ajaxLink) {
            $attributes['container'] = [...$attributes['container'], 'class' => ('ajaxlink ' . $attributes['container']['class'])];
        }

        $blockBody = $this->clearTag('div', $data, $attributes['col']);

        return $this->tag($href ? 'a' : 'div', $blockBody, $attributes['container']);
    }

    /**
     * Cols z nagłówkiem tylko do odczytu
     * @param string $title
     * @param mixed $message
     * @param array $attribute dodatkowe attrybuty treści
     * @param ?int $col Ilość kolumn w wierszu
     * @return $this
     * @throws MicroFrameworkException
     */
    public function colInfo(string $title = '', mixed $message = '', array $attribute = [], ?int $col = 4) : Html {
        $title = $this->clearTag('h6', $title, ['class' => 'p-0 m-0 user-select-none fw-bold']);
        $message = $this->clearTag('p', $message, ['class' => 'p-0 m-0 mb-2', ...$attribute]);

        if ($col !== null) {
            $col = 'col-' . $col;
        } else {
            $col = 'col';
        }

        return $this->tag('div', $title . $message, ['class' => $col]);
    }

    /**
     * Ikona
     * @param string $icon klasa ikony
     * @param ?string $title tytuł po najechaniu myszką
     * @param ?string $color kolor tekstu, (text-{color})
     * @param array $attributes atrybuty
     * @param bool $show czy ma być zwrócony
     * @return string
     * @throws MicroFrameworkException
     */
    public function icon(string $icon, ?string $title = null, ?string $color = null, array $attributes = [], bool $show = true) : string {
        if (!$show) {
            return '';
        }

        $tag = 'i';

        $attributes['class'] = ($attributes['class'] ?? '') . ' ms-1 ' . $icon . ' ' . ($color ? ('text-' . $color) : '');

        if (array_key_exists('href', $attributes)) {
            $tag = 'a';
            $attributes['class'] .= ' text-decoration-none';
        }

        if ($title) {
            $attributes['title'] = $title;
        }

        return $this->clearTag($tag, '', $attributes);
    }

}