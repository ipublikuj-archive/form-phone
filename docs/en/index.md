# Quickstart

This extension extend your [Nette](http://nette.org) forms with phone control field with specific phone number validation, based on [iPublikuj:Phone!](https://github.com/iPublikuj/phone)

## Installation

The best way to install ipub/form-phone is using  [Composer](http://getcomposer.org/):

```sh
$ composer require ipub/form-phone
```

After that you have to register extension in config.neon.

```neon
extensions:
	formPhone: IPub\FormPhone\DI\FormPhoneExtension
```

And you also need to include static files into your page:

```html
	<script src="{$basePath}/libs/ipub.formPhone.js"></script>
</body>
```

note: You have to upload static files from **client-site** folder to your project.

## Usage

```php
$form->addPhone('phone', 'Phone number:');
```

This control return values as normal text input, so you can access your phone like this:

```php
$phone = $form->values->phone;
```

Returned value is instance of IPub\Phone\Entities\Phone or null if the given phone number is not valid.

This control will render two elements, one select box where you can choose country prefix and one text box where you put your national part of number.

### Validation

Field can be validated as usual text fields in nette, but phone number can be validated with custom validator:

```php
$form->addPhone('phone', 'Phone number:')
    ->addCondition(\Nette\Application\UI\Form::FILLED)
        ->addRule(\IPub\FormPhone\Forms\PhoneValidator::PHONE, 'Phone is invalid');
```

#### Limit to country

By default is country detection set to AUTO, if you want to specify allowed country/ies you can set them:

```php
$form->addPhone('phone', 'Phone number:')
    ->addCountry('CZ')
    ->addCountry('GB')
    ->addCondition(\Nette\Application\UI\Form::FILLED)
        ->addRule(\IPub\FormPhone\Forms\PhoneValidator::PHONE, 'Phone is invalid');
```

or

```php
$form->addPhone('phone', 'Phone number:')
    ->setCountries(['CZ', 'GB'])
    ->addCondition(\Nette\Application\UI\Form::FILLED)
        ->addRule(\IPub\FormPhone\Forms\PhoneValidator::PHONE, 'Phone is invalid');
```

Now only phone numbers from Czech Republic or Great Britain are allowed.

#### Limit to phone type

You can limit allowed phone to specific phone types eg. mobile, land line etc.

```php
$form->addPhone('phone', 'Phone number:')
    ->addPhoneType(\IPub\Phone\Phone::TYPE_MOBILE)
    ->addCondition(\Nette\Application\UI\Form::FILLED)
        ->addRule(\IPub\FormPhone\Forms\PhoneValidator::PHONE, 'Phone is invalid');
```

or

```php
$form->addPhone('phone', 'Phone number:')
    ->setPhoneTypes([\IPub\Phone\Phone::TYPE_MOBILE, \IPub\Phone\Phone::TYPE_PAGER])
    ->addCondition(\Nette\Application\UI\Form::FILLED)
        ->addRule(\IPub\FormPhone\Forms\PhoneValidator::PHONE, 'Phone is invalid');
```

List of allowed phone types is available in [iPublikuj:Phone!](https://github.com/iPublikuj/phone/blob/master/src/IPub/Phone/Phone.php#L39-L47)

### Manual rendering

Phone field can be rendered as usual, or manually with partial rendering:

```html
{form yourFormWithPhoneField}
    // ...

    {label phone /}
    {input phone:country}
    {input phone:number}

    // ...
{/form}
```
