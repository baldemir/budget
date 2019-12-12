---
title: API Reference

language_tabs:
- bash
- javascript

includes:

search: true

toc_footers:
- <a href='http://github.com/mpociot/documentarian'>Documentation Powered by Documentarian</a>
---
<!-- START_INFO -->
# Info

Welcome to the generated API reference.
[Get Postman Collection](http://kolaybutce.com/docs/collection.json)

<!-- END_INFO -->

#general
<!-- START_1ba53c50d1711ea4aa2c84e2e0a2eab5 -->
## api/getDailyTransactions
> Example request:

```bash
curl -X GET -G "https://kolaybutce.com/api/getDailyTransactions" \
    -H "Authorization: Bearer {token}" \
    -H "Content-Type: application/json" \
    -d '{"year":13,"month":13,"day":7}'

```

```javascript
const url = new URL("https://kolaybutce.com/api/getDailyTransactions");

let headers = {
    "Authorization": "Bearer {token}",
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "year": 13,
    "month": 13,
    "day": 7
}

fetch(url, {
    method: "GET",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (401):

```json
{
    "error": "Unauthenticated."
}
```

### HTTP Request
`GET api/getDailyTransactions`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    year | integer |  required  | The specified year.
    month | integer |  required  | The specified month.
    day | integer |  required  | The specified day of month.

<!-- END_1ba53c50d1711ea4aa2c84e2e0a2eab5 -->

<!-- START_ccca629322b53c27dd79b31d3c5a32f7 -->
## api/getMonthlyTransactions
> Example request:

```bash
curl -X GET -G "https://kolaybutce.com/api/getMonthlyTransactions" \
    -H "Authorization: Bearer {token}" \
    -H "Content-Type: application/json" \
    -d '{"year":13,"month":17}'

```

```javascript
const url = new URL("https://kolaybutce.com/api/getMonthlyTransactions");

let headers = {
    "Authorization": "Bearer {token}",
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "year": 13,
    "month": 17
}

fetch(url, {
    method: "GET",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (401):

```json
{
    "error": "Unauthenticated."
}
```

### HTTP Request
`GET api/getMonthlyTransactions`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    year | integer |  required  | The specified year.
    month | integer |  required  | The specified month.

<!-- END_ccca629322b53c27dd79b31d3c5a32f7 -->

<!-- START_93d10e10a29b8b526f65b761e898e672 -->
## api/getMonthlyEarnings
> Example request:

```bash
curl -X GET -G "https://kolaybutce.com/api/getMonthlyEarnings" \
    -H "Authorization: Bearer {token}" \
    -H "Content-Type: application/json" \
    -d '{"year":17,"month":9}'

```

```javascript
const url = new URL("https://kolaybutce.com/api/getMonthlyEarnings");

let headers = {
    "Authorization": "Bearer {token}",
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "year": 17,
    "month": 9
}

fetch(url, {
    method: "GET",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (401):

```json
{
    "error": "Unauthenticated."
}
```

### HTTP Request
`GET api/getMonthlyEarnings`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    year | integer |  required  | The specified year.
    month | integer |  required  | The specified month.

<!-- END_93d10e10a29b8b526f65b761e898e672 -->

<!-- START_5a371f07c8675ec3300f89a8dc12c591 -->
## api/getMonthlySpendings
> Example request:

```bash
curl -X GET -G "https://kolaybutce.com/api/getMonthlySpendings" \
    -H "Authorization: Bearer {token}" \
    -H "Content-Type: application/json" \
    -d '{"year":13,"month":17}'

```

```javascript
const url = new URL("https://kolaybutce.com/api/getMonthlySpendings");

let headers = {
    "Authorization": "Bearer {token}",
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "year": 13,
    "month": 17
}

fetch(url, {
    method: "GET",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (401):

```json
{
    "error": "Unauthenticated."
}
```

### HTTP Request
`GET api/getMonthlySpendings`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    year | integer |  required  | The specified year.
    month | integer |  required  | The specified month.

<!-- END_5a371f07c8675ec3300f89a8dc12c591 -->

<!-- START_d036b8aace5560ad16211d5103237fd3 -->
## api/getMonthlySummary
> Example request:

```bash
curl -X GET -G "https://kolaybutce.com/api/getMonthlySummary" \
    -H "Authorization: Bearer {token}" \
    -H "Content-Type: application/json" \
    -d '{"year":14,"month":2}'

```

```javascript
const url = new URL("https://kolaybutce.com/api/getMonthlySummary");

let headers = {
    "Authorization": "Bearer {token}",
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "year": 14,
    "month": 2
}

fetch(url, {
    method: "GET",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (401):

```json
{
    "error": "Unauthenticated."
}
```

### HTTP Request
`GET api/getMonthlySummary`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    year | integer |  required  | The specified year.
    month | integer |  required  | The specified month.

<!-- END_d036b8aace5560ad16211d5103237fd3 -->

<!-- START_b9805f9a5588059d0c0d57c5f05f45ce -->
## api/getMonthlyCategories
> Example request:

```bash
curl -X GET -G "https://kolaybutce.com/api/getMonthlyCategories" \
    -H "Authorization: Bearer {token}" \
    -H "Content-Type: application/json" \
    -d '{"year":6,"month":14}'

```

```javascript
const url = new URL("https://kolaybutce.com/api/getMonthlyCategories");

let headers = {
    "Authorization": "Bearer {token}",
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "year": 6,
    "month": 14
}

fetch(url, {
    method: "GET",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (401):

```json
{
    "error": "Unauthenticated."
}
```

### HTTP Request
`GET api/getMonthlyCategories`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    year | integer |  required  | The specified year.
    month | integer |  required  | The specified month.

<!-- END_b9805f9a5588059d0c0d57c5f05f45ce -->

<!-- START_191f3bfb75684719c820ab8ad8fa0cb2 -->
## api/getUserCategories
> Example request:

```bash
curl -X GET -G "https://kolaybutce.com/api/getUserCategories" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("https://kolaybutce.com/api/getUserCategories");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (401):

```json
{
    "error": "Unauthenticated."
}
```

### HTTP Request
`GET api/getUserCategories`


<!-- END_191f3bfb75684719c820ab8ad8fa0cb2 -->

<!-- START_de8c14bf8c667d39af01ea1e6fcc847a -->
## api/getUser
> Example request:

```bash
curl -X GET -G "https://kolaybutce.com/api/getUser" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("https://kolaybutce.com/api/getUser");

let headers = {
    "Authorization": "Bearer {token}",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (401):

```json
{
    "error": "Unauthenticated."
}
```

### HTTP Request
`GET api/getUser`


<!-- END_de8c14bf8c667d39af01ea1e6fcc847a -->

<!-- START_64b4b376384828178185ada57582a3e1 -->
## api/setUserImage
> Example request:

```bash
curl -X POST "https://kolaybutce.com/api/setUserImage" \
    -H "Authorization: Bearer {token}" \
    -H "Content-Type: application/json" \
    -d '{"avatar":"veniam"}'

```

```javascript
const url = new URL("https://kolaybutce.com/api/setUserImage");

let headers = {
    "Authorization": "Bearer {token}",
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "avatar": "veniam"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (401):

```json
{
    "error": "Unauthenticated."
}
```

### HTTP Request
`POST api/setUserImage`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    avatar | file |  required  | The photo for user.

<!-- END_64b4b376384828178185ada57582a3e1 -->

<!-- START_ff47f10adcd3d80d9f512f95eda6ff6b -->
## api/saveTransaction
> Example request:

```bash
curl -X POST "https://kolaybutce.com/api/saveTransaction" \
    -H "Authorization: Bearer {token}" \
    -H "Content-Type: application/json" \
    -d '{"tag_id":18,"happened_on":"repudiandae","description":"veniam","amount":8}'

```

```javascript
const url = new URL("https://kolaybutce.com/api/saveTransaction");

let headers = {
    "Authorization": "Bearer {token}",
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "tag_id": 18,
    "happened_on": "repudiandae",
    "description": "veniam",
    "amount": 8
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (401):

```json
{
    "error": "Unauthenticated."
}
```

### HTTP Request
`POST api/saveTransaction`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    tag_id | integer |  required  | Category id of transaction.
    happened_on | string |  required  | Date of transaction in format of YYY-mm-dd.
    description | string |  required  | The description(name) of transaction.
    amount | integer |  required  | The amount of transaction.

<!-- END_ff47f10adcd3d80d9f512f95eda6ff6b -->

<!-- START_e9285fdb83c137e9c5a10f46e139ef64 -->
## api/getMonthlyTransactionsByCategory
> Example request:

```bash
curl -X GET -G "https://kolaybutce.com/api/getMonthlyTransactionsByCategory" \
    -H "Authorization: Bearer {token}" \
    -H "Content-Type: application/json" \
    -d '{"year":14,"month":4,"categoryId":6}'

```

```javascript
const url = new URL("https://kolaybutce.com/api/getMonthlyTransactionsByCategory");

let headers = {
    "Authorization": "Bearer {token}",
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "year": 14,
    "month": 4,
    "categoryId": 6
}

fetch(url, {
    method: "GET",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (401):

```json
{
    "error": "Unauthenticated."
}
```

### HTTP Request
`GET api/getMonthlyTransactionsByCategory`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    year | integer |  required  | The specified year.
    month | integer |  required  | The specified month.
    categoryId | integer |  required  | The specified category id.

<!-- END_e9285fdb83c137e9c5a10f46e139ef64 -->

<!-- START_01ad17aef4cd45cdba2ae5ae2050e8de -->
## api/extensionLogin
> Example request:

```bash
curl -X POST "https://kolaybutce.com/api/extensionLogin" \
    -H "Authorization: Bearer {token}" \
    -H "Content-Type: application/json" \
    -d '{"email":"iusto","password":"nam"}'

```

```javascript
const url = new URL("https://kolaybutce.com/api/extensionLogin");

let headers = {
    "Authorization": "Bearer {token}",
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "email": "iusto",
    "password": "nam"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "resultText": "Authentication error.",
    "resultCode": "GUPPY.701",
    "content": "Authentication Error 2."
}
```

### HTTP Request
`POST api/extensionLogin`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    email | string |  required  | Email of user.
    password | string |  required  | Password of user.

<!-- END_01ad17aef4cd45cdba2ae5ae2050e8de -->

<!-- START_d53d217f20a48e4022e531901041d531 -->
## api/loginWithFacebookToken
> Example request:

```bash
curl -X POST "https://kolaybutce.com/api/loginWithFacebookToken" \
    -H "Authorization: Bearer {token}" \
    -H "Content-Type: application/json" \
    -d '{"token":"vel"}'

```

```javascript
const url = new URL("https://kolaybutce.com/api/loginWithFacebookToken");

let headers = {
    "Authorization": "Bearer {token}",
    "Content-Type": "application/json",
    "Accept": "application/json",
}

let body = {
    "token": "vel"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (500):

```json
{
    "message": "Server Error"
}
```

### HTTP Request
`POST api/loginWithFacebookToken`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    token | string |  required  | Token acquired from facebook login attempt.

<!-- END_d53d217f20a48e4022e531901041d531 -->


