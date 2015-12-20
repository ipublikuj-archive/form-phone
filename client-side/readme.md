## Javascript API Documentation

API for Phone is accessible in global object `window.IPub.Forms.Phone`.

### Loading

Serverside part of Phone form element is element with custom data attribute `data-ipub-forms-phone`. This element can be initialized with method `init()`.

```js
var phone = new IPub.Forms.Phone($('[data-ipub-forms-phone]'));
    phone.init();
```

But there is shortcut implemented as jQuery plugin:

```js
$('[data-ipub-forms-phone]').ipubFormsPhone();
```

You can chain other jQuery methods after this as usual. If you try to initialize one Phone twice, it will fail silently (second initialization won't proceed).

Finally you can initialize phone field on the page by calling:

```js
IPub.Forms.Phone.load();
```

This will be automatically called when document is ready.
