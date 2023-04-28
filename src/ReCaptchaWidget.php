<?php

namespace rabdavinci\recaptcha3;

use Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * Class ReCaptchaWidget
 * @package rabdavinci\recaptcha3
 */
class ReCaptchaWidget extends InputWidget
{
  /**
   * Recaptcha component
   * @var string|array|ReCaptcha
   */
  public $component = 'reCaptcha3';

  /**
   * @var string
   */
  public $buttonText = 'Submit';

  /**
   * @var string
   */
  public $actionName = 'homepage';

  /**
   * @var RecaptchaV3
   */
  private $_component = null;

  /**
   * @inheritdoc
   * @throws InvalidConfigException
   */
  public function init()
  {
    parent::init();
    $component = Instance::ensure($this->component, ReCaptcha::class);
    if ($component == null) {
      throw new InvalidConfigException('component is required.');
    }
    $this->_component = $component;
  }


  /**
   * @inheritdoc
   */
  public function run()
  {
    //  $this->_component->registerScript($this->getView());
    $this->field->template = "{input}\n{error}";
    $formId = $this->field->form->id;
    $inputId = Html::getInputId($this->model, $this->attribute);
    $callbackRandomString = time();

    $options = array_merge([
      //  'onClick' => "recaptchaCallback_{$callbackRandomString}()"
    ], $this->options);

    $jsCode = <<<JS
        if(typeof grecaptcha === 'undefined') {
          grecaptcha = parent.grecaptcha;
        }
    grecaptcha.ready(function () {
      grecaptcha.execute('{$this->_component->site_key}', {action: '{$this->actionName}'}).then(function (token) {
        $('#{$inputId}').val(token);
      });
    });
    $('#{$formId}').on('beforeSubmit', function () {
      if (!$('#{$inputId}').val()) {
        grecaptcha.ready(function () {
          grecaptcha.execute('{$this->_component->site_key}', {action: '{$this->actionName}'}).then(function (token) {
            $('#{$inputId}').val(token);
            $('#{$formId}').submit();
          });
        });
        return false;
      } else {
        return true;
      }
    });
JS;

    $this->view->registerJs($jsCode, View::POS_END);

    return Html::activeHiddenInput($this->model, $this->attribute, ['value' => '']);
  }
}
