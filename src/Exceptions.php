<?php

namespace Dice\Exception;

class DiceException extends \Exception {}

class DiceAuthException extends DiceException {}

class DiceTokenExpiredException extends DiceException {}

class DiceNewIPException extends DiceException {}

class DiceTemplateException extends DiceException {}

class DiceValidationException extends DiceException {}

class DiceConnectionException extends DiceException {}