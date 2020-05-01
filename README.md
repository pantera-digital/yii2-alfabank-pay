# yii2-alfabank-pay

### Установка через композер
```
composer require pantera-digital/yii2-alfabank-pay "@dev"
```

### Запустить миграции
```
php yii migrate --migrationPath=@pantera/yii2/pay/alfabank/migrations
```

### Настройка, добавить в config/main.php

```
'bootstrap' => ['alfabank'],
'modules' => [
    'alfabank' => [
        'class' => pantera\yii2\pay\alfabank\Module::class,
        'components' => [
            'alfabank' => [
                'class' => pantera\yii2\pay\alfabank\components\Alfabank::class,
                
                // время жизни инвойса в секундах (по умолчанию 20 минут - см. документацию Альфабанка)
                // в этом примере мы ставим время 1 неделю, т.е. в течение этого времени покупатель может
                // произвести оплату по выданной ему ссылке
                'sessionTimeoutSecs' => 60 * 60 * 24 * 7,
                
                // логин api мерчанта
                'login' => 'ваш логин',
                
                // пароль api мерчанта
                'password' => 'ваш пароль',
            ],
        ],
        
        // страница вашего сайта с информацией об успешной оплате
        'successUrl' => '/paySuccess',
        
        // страница вашего сайта с информацией о НЕуспешной оплате
        'failUrl' => '/payFail',
        
        // обработчик, вызываемый по факту успешной оплаты
        'successCallback' => function($invoice){
            // какая-то ваша логика, например
            $order = \your\models\Order::findOne($invoice->order_id);
            $client = $order->getClient();
            $client->sendEmail('Зачислена оплата по вашему заказу №' . $order->id);
            // .. и т.д.
        },

        // необязательный callback для генерации uniqid инвойса, необходим
        // в том случае, если по каким-то причинам используемый по умолчанию
        // формат `#invoice_id#-#timestamp#` вам не подходит
        'idGenerator' => function(Invoice $invoice, int $id) {
            // $id - это uniqid, сгенерированный по умолчанию
            // вместо него используем собственный алгоритм, например такой
            return '000-AAA-' . $invoice->id;
        },
    ],
]
```

### Создание заказа

В вашем контроллере после сохранения заказа, либо на событие создания заказа вам необходимо создать инвойс, передав в него номер и сумму вашего заказа:

```
// ...здесь какая-то ваша логика по сохранению заказа, например это объект $order

// создаем и сохраняем инвойс, передаем в него номер и сумму вашего заказа
$invoice = \pantera\yii2\pay\alfabank\models\Invoice::addAlfabank($order->id, $order->price);
```

Далее для перенаправления пользователя на шлюз оплаты Альфабанка вам нужно выдать пользователю ссылку (либо автоматически перенаправить его) на url:

```
\yii\helpers\Html::a('Оплатить заказ', ['/alfabank/default/create', 'id' => $invoice->id /* id инвойса */])
```

При этом при переходе пользователя по этой ссылке (либо автоматическом перенаправлении) будет произведено обращение к API альфабанка для создания инвойса у них в системе, и перенаправление уже на платежную форму Альфабанка.

После успешной оплаты на шлюзе Альфабанка пользователь будет преренаправлен на `yoursite.com/paySuccess`. В случае неуспешной оплаты пользователь будет преренаправлен на `yoursite.com/payFail`. `paySuccess` и `payFail` задаются в настройках модуля, см. пример конфигурации.

### Статусы инвойсов
```
I - initial, инвойс создан
S - success, успешно оплачен
```
