<?php
/**
 * TbNavbar class file.
 * @author Christoffer Niska <christoffer.niska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package bootstrap/widgets
 */



/**
 * Bootstrap navbar widget.
 * @see http://twitter.github.com/bootstrap/components.html#navbar
 */
class BsNavbar extends CWidget
{
    /**
     * @var string the navbar color.
     */
    public $color = BSHtml::NAVBAR_COLOR;
    /**
     * @var string the brand label text.
     */
    public $brandLabel;
    /**
     * @var mixed the brand url.
     */
    public $brandUrl;
    /**
     * @var array the HTML attributes for the brand link.
     */
    public $brandOptions = array();
    /**
     * @var string nanvbar display type.
     */
    public $position = BSHtml::NAVBAR_POSITION;
    /**
     * @var boolean whether to enable collapsing of the navbar on narrow screens.
     */
    public $collapse = false;
    /**
     * @var array additional HTML attributes for the collapse widget.
     */
    public $collapseOptions = array();
    /**
     * @var array list of navbar item.
     */
    public $items = array();
    /**
     * @var array the HTML attributes for the navbar.
     */
    public $htmlOptions = array();

    /**
     * Initializes the widget.
     */
    public function init()
    {
        if ($this->brandLabel !== false) {
            if (!isset($this->brandLabel)) {
                $this->brandLabel = CHtml::encode(Yii::app()->name);
            }

            if (!isset($this->brandUrl)) {
                $this->brandUrl = Yii::app()->homeUrl;
            }
        }
        if (isset($this->color)) {
            \bootstrap\helpers\BSArray::defaultValue('color', $this->color, $this->htmlOptions);
        }
        if (isset($this->position) && $this->position !== BSHtml::NAVBAR_POSITION) {
            \bootstrap\helpers\BSArray::defaultValue('position', $this->position, $this->htmlOptions);
        }
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        $brand = $this->brandLabel !== false
            ? BSHtml::navbarBrandLink($this->brandLabel, $this->brandUrl, $this->brandOptions)
            : '';
        ob_start();
        foreach ($this->items as $item) {
            if (is_string($item)) {
                echo $item;
            } else {
                $widgetClassName = \bootstrap\helpers\BSArray::popValue('class', $item);
                if ($widgetClassName !== null) {
                    $this->controller->widget($widgetClassName, $item);
                }
            }
        }
        $items = ob_get_clean();
        ob_start();
        if ($this->collapse !== false) {
            BSHtml::addCssClass('collapse navbar-collapse', $this->collapseOptions);
            ob_start();
            /* @var BsCollapse $collapseWidget */
            $collapseWidget = $this->controller->widget(
                'bootstrap.widgets.BsCollapse',
                array(
                    'toggle' => false, // navbars are collapsed by default
                    'content' => $items,
                    'htmlOptions' => $this->collapseOptions,
                )
            );
            $collapseContent = ob_get_clean();
            $collapseLink =  BSHtml::navbarCollapseLink('#' . $collapseWidget->getId());

            echo BSHtml::tag('div',array('class'=>'navbar-header'),$collapseLink . $brand) . $collapseContent;

        } else {
            echo BSHtml::tag('div',array('class'=>'navbar-header'),$brand) . $items;
        }
        $containerContent = ob_get_clean();
        $containerOptions = \bootstrap\helpers\BSArray::popValue('containerOptions', $this->htmlOptions, array());
        BSHtml::addCssClass( 'container', $containerOptions);

        $content = BSHtml::tag('div',$containerOptions,$containerContent);
        echo BSHtml::navbar($content, $this->htmlOptions);
    }
}