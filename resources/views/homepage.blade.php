<!DOCTYPE html>
<html>
<head>
    <title>Kolay Bütçe</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, Helvetica, Roboto, Arial;
        }

        h1, h2 {
            font-weight: 400;
        }

        p {
            line-height: 150%;
        }

        a {
            color: #179BD1;
        }

        /* Temporary, remove this */
        ul.wrapper {
            list-style: none;
        }

        ul.wrapper li {
            margin-top: 20px;
        }

        ul.wrapper li:first-child {
            margin-top: 0;
        }

        .navigation {
            margin-left: auto;
            margin-right: auto;
            display: flex;
            justify-content: flex-end;
            width: 90%;
            max-width: 800px;
            list-style: none;
        }

        .navigation li {
            margin-left: 20px;
        }

        .navigation li:first-child {
            margin-left: 0;
        }

        .wrapper {
            margin-left: auto;
            margin-right: auto;
            width: 90%;
            max-width: 800px;
        }

        .row {
            display: flex;
        }

        .row--center {
            justify-content: center;
        }

        .row__column {
            flex: 1;
        }

        .button {
            padding: 10px 20px;
            display: inline-block;
            text-decoration: none;
            background: #179BD1;
            color: #FFF;
            font-size: 14px;
            border-radius: 5px;
            border-bottom: 2px solid rgba(0, 0, 0, .2);
        }

        .button.button--outline {
            background: none;
            color: #179BD1;
            border: 1px solid #179BD1;
        }

        .button.button--big {
            padding: 15px 30px;
            min-width: 100px;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-14 {
            font-size: 14px;
        }

        .my-8 {
            margin-top: 80px;
            margin-bottom: 80px;
        }

        .mt-8 {
            margin-top: 80px;
        }

        .mt-4 {
            margin-top: 40px;
        }

        .mt-2 {
            margin-top: 20px;
        }

        .mt-1 {
            margin-top: 10px;
        }

        .mb-8 {
            margin-bottom: 80px;
        }

        .mb-4 {
            margin-bottom: 40px;
        }

        .mb-2 {
            margin-bottom: 20px;
        }

        .ml-4 {
            margin-left: 40px;
        }

        .mr-1 {
            margin-right: 10px;
        }

        .c-primary {
            color: #179BD1;
        }

        .card {
            padding: 20px;
            border: 1px solid #E4E8EB;
            border-top: 2px solid #179BD1;
            box-shadow: 0 5px 10px rgba(0, 0, 0, .05);
            border-radius: 5px;
        }
    </style>
</head>
<body>
<ul class="navigation mt-2 mb-8">
    <li>
        <a href="/login" class="button button--outline">Giriş</a>
    </li>
    <li>
        <a href="/register" class="button">Kayıt Ol</a>
    </li>
</ul>
<div class="text-center my-8">
    <h1>Kolay Bütçe 🚀</h1>
    <h2 class="mt-2 mb-4">Bütçenizi kontrol etmek o kadar da zor değil.</h2>
    <a href="/register" class="button button--big">Kayıt Ol</a>
    <p class="text-14 mt-2">Tamamen ücretsiz. Bugün ve daima.</p>
</div>
<div class="wrapper row row--center mb-8">
    <div class="row__column">
        <div class="card">
            <h2>2.214</h2>
            <div class="text-14 mt-1">Kayıtlı Kullanıcı</div>
        </div>
    </div>
    <div class="row__column ml-4">
        <div class="card">
            <h2>141.882</h2>
            <div class="text-14 mt-1">Gelir Gider Kaydı</div>
        </div>
    </div>
</div>
<ul class="wrapper">
    <li>
        <p><i class="fas fa-check c-primary mr-1"></i> Banka hesap hareketlerinizi otomatik olarak içe aktarma.</p>
    </li>
    <li>
        <p><i class="fas fa-check c-primary mr-1"></i> Cepteteb ve Garanti BBVA internet bankacılığı ile tam entegrasyon.</p>
    </li>
    <li>
        <p><i class="fas fa-check c-primary mr-1"></i> Tema seçenekleri.</p>
    </li>
    <li>
        <p><i class="fas fa-check c-primary mr-1"></i> Harcamalarınızla ilgili haftalık ve aylık raporlar.</p>
    </li>
</ul>
<div class="wrapper mt-8" style="max-width: 800px;">
    <img src="/storage/dashboard.png" style="width: 100%; vertical-align: top; border-radius: 5px;" />
</div>
<div class="text-center mb-2">

</div>
</body>
</html>
