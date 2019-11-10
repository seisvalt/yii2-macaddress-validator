<?php

/**
 * Yii2 MAC-address Validator
 * 
 * @link https://github.com/seisvalt/yii2-macaddress-validator
 * @license https://github.com/seisvalt/yii2-macaddress-validator/blob/master/LICENSE MIT
 * @author Vladimir Korovin <jose.vargas@mingenio.com>
 */

namespace vakorovin\yii2_macaddress_validator;

use yii\validators\Validator;

use Yii;

class MacaddressValidator extends Validator
{

	public $patterns = [
		'/^[0-9a-f]{2}[\-: ]{1}[0-9a-f]{2}[\-: ]{1}[0-9a-f]{2}[\-: ]{1}[0-9a-f]{2}[\-: ]{1}[0-9a-f]{2}[\-: ]{1}[0-9a-f]{2}$/i',
		'/^[0-9a-f]{4}[\. ]{1}[0-9a-f]{4}[\. ]{1}[0-9a-f]{4}$/i',
		'/^[0-9a-f]{6}[\-: ]{1}[0-9a-f]{6}$/i',
		'/^[0-9a-f]{12}$/i',
	];

    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} is not a valid mac-address.');
        }
    }

    public function validateAttribute($model, $attribute)
    {
        $result = $this->validateValue($model->$attribute);
        if (!empty($result)) {
            $this->addError($model, $attribute, $result[0], $result[1]);
        } else {
			$m = preg_replace("/[^0-9a-f]/i", "", $model->$attribute);
			$model->$attribute = strtoupper(substr($m,0,2).':'.substr($m,2,2).':'.substr($m,4,2).':'.substr($m,6,2).':'.substr($m,8,2).':'.substr($m,10,2));
		}
    }

    protected function validateValue($value)
    {

		foreach ($this->patterns as $pattern){
			if (preg_match($pattern, $value)){
				return null;
			}
		}

        return [$this->message, []];

    }

    public function clientValidateAttribute($model, $attribute, $view)
    {

		$message=Yii::$app->getI18n()->format($this->message, [
                'attribute' => $model->getAttributeLabel($attribute),
            ], Yii::$app->language);

		return "
            var message='".$message."';

			var patterns=[
				/^[0-9a-f]{2}[\-: ]{1}[0-9a-f]{2}[\-: ]{1}[0-9a-f]{2}[\-: ]{1}[0-9a-f]{2}[\-: ]{1}[0-9a-f]{2}[\-: ]{1}[0-9a-f]{2}$/i,
				/^[0-9a-f]{4}[\. ]{1}[0-9a-f]{4}[\. ]{1}[0-9a-f]{4}$/i,
				/^[0-9a-f]{6}[\-: ]{1}[0-9a-f]{6}$/i,
				/^[0-9a-f]{12}$/i,
			];

			var valid=false;

			for (i in patterns){
				matches=patterns[i].exec(value);
				if (matches!=null){
					return true;
				}
			}

			yii.validation.addMessage(messages, message, value);

		";

    }

}

