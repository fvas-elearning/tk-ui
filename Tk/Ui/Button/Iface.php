<?php
namespace Tk\Ui\Button;


/**
 * Class Iface
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 */
class Iface
{
    use \Tk\Dom\AttributesTrait;
    use \Tk\Dom\CssTrait;
    use \Tk\CollectionTrait;

    /**
     * @var int
     */
    protected static $idx = 0;
    

    /**
     * @var int
     */
    protected $id = 0;
    /**
     * NOTE: This is not the attribute title use setAttr()
     * @var string
     */
    protected $title = '';
    /**
     * @var string
     */
    protected $text = '';
    /**
     * @var null|\Tk\Uri
     */
    protected $url = null;
    /**
     * icon css 'fa fa-user'
     * @var string
     */
    protected $icon = '';
    /**
     * @var boolean
     */
    protected $visible = true;

    /**
     * @var null|callable
     */
    protected $onShow = null;


    /**
     * Iface constructor.
     * @param string $title
     * @param null|\Tk\Url|string $url
     * @param null|callable $onShow
     */
    public function __construct($title, $url = '#', $onShow = null)
    {
        $this->id = self::$idx++;
        $this->title = $title;
        $this->url = $url;
        $this->onShow = $onShow;
    }

    /**
     * @param string $title
     * @param null|\Tk\Uri $url
     * @param null|callable $onShow
     * @return Iface
     */
    public static function create($title, $url = null, $onShow = null)
    {
        $obj = new self($title, $url, $onShow);
        return $obj;
    }


    /**
     * @return \Dom\Template
     */
    public function show()
    {
        /** @var \Dom\Template $template */
        $template = $this->getTemplate();

        // callback
        if ($this->hasOnShow()) {
            call_user_func_array($this->getOnShow(), array($this));
        }

        if (!$this->isVisible()) return $template;
        $template->setChoice('btn');

        if ($this->getTitle()) {
            $template->insertText('title', $this->getTitle());
        }
        if ($this->getUrl()) {
            $template->setAttr('btn', 'href', $this->getUrl());
        }
        if ($this->getIcon()) {
            $template->addCss('icon', $this->getIcon());
        }
        $css = $this->getCssString();
        if (!$css) {
            $css = 'btn btn-default';
        }
        $template->addCss('btn', $css);
        if (count($this->getAttrList())) {
            $template->setAttr('btn', $this->getAttrList());
        }
        return $template;
    }

    /**
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $html = <<<HTML
<a href="#" class="btn" var="btn" choice="btn"><i var="icon" choice="icon"></i> <span var="title"></span></a>
HTML;
        return \Dom\Loader::load($html);
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return null|\Tk\Uri
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param \Tk\Uri|string $url
     * @return $this
     */
    public function setUrl($url)
    {
        if ($url !== null) {
            $this->url = \Tk\Uri::create($url);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return callable|null
     */
    public function getOnShow()
    {
        return $this->onShow;
    }

    /**
     * function (\Tk\Ui\Button\Iface $button) {}
     *
     * @param callable|null $onShow
     */
    public function setOnShow($onShow)
    {
        $this->onShow = $onShow;
    }

    /**
     * @return bool
     */
    public function hasOnShow()
    {
        return is_callable($this->getOnShow());
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     * @return $this
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }

}