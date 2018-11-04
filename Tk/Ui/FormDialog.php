<?php
namespace Tk\Ui;



/**
 * This class uses the bootstrap dialog box model
 * @link http://getbootstrap.com/javascript/#modals
 *
 * To add a close button to the footer:
 *
 *    $dialog->getButtonList()->append(\Tk\Ui\Button::createButton('Close')->setAttr('data-dismiss', 'modal'));
 *
 * Launch Button:
 *
 *    <a href="#" data-toggle="modal" data-target="#{id}"><i class="fa fa-info-circle"></i> {title}</a>
 *
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
abstract class FormDialog extends Dialog
{


    /**
     * @var \Tk\Form
     */
    protected $form = null;


    /**.
     * @param \Tk\Form $form
     * @param string $title
     */
    public function __construct($form, $title = '')
    {
        $this->setLarge(true);
        $this->setForm($form);
        $this->id = $this->form->getId().'-dialog';
        if (!$title)    // TODO: Not sure if this is correct???
            $title = ucwords(preg_replace('/[A-Z_-]/', ' $0', $title));
        $this->setTitle($title);
        $this->setButtonList(\Tk\Ui\ButtonCollection::create());

        $this->setAttr('id', $this->getId());
        $this->setAttr('aria-labelledby', $this->getId().'-Label');

        if ($this->form->getField('cancel')) {
            $this->form->getField('cancel')->addCss('float-right')->setAttr('data-dismiss', 'modal');
        }
        if ($this->form->getField('save')) {
            $this->form->getField('save')->addCss('float-right')->setIconLeft('')->setIconRight('fa fa-arrow-right');
        }
        if ($this->form->getField('update')) {
            $this->form->removeField('update');
        }

        $this->init();
    }

    /**
     * @param \Tk\Form $form
     * @param string $title
     * @return static
     */
    public static function createFormDialog($form, $title = '')
    {
        $obj = new static($form, $title);
        return $obj;
    }

    /**
     * @return \Tk\Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param \Tk\Form $form
     * @return $this
     */
    public function setForm(\Tk\Form $form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $dialogTemplate = parent::show();

        $js = <<<JS
jQuery(function ($) {
  
  function init() {
    var form = $(this);
    form.on('submit', function (e) {
      e.preventDefault();  // prevent form from submitting
      var f = $(this);
      f.append('<input type="hidden" name="'+f.attr('id')+'-save" value="'+f.attr('id')+'-save" />');
      $.post(f.attr('action'), f.serialize(), function (html) {
        console.log(html);
        var newEl = $(html).find('#'+f.attr('id'));
        if (!newEl.length) {
          console.error('Error: From not submitted. Invalid response from server.');
          return false;
        }
        f.empty().append(newEl.find('> div'));
        f.trigger('init');
        
        if (!f.find('.tk-is-invalid, .alert-danger').length) {
          // if success then we need to close the dialog and reload the page.
          document.location = f.attr('action');          
        }
      }, 'html');
      return false;
    });
  }
  
  $('.modal-body form').on('init', '.modal-dialog', init).each(init);
});
JS;
        $dialogTemplate->appendJs($js);


        return $dialogTemplate;
    }


}
