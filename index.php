<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $length = filter_input(INPUT_POST, 'length', FILTER_VALIDATE_INT);
    $width = filter_input(INPUT_POST, 'width', FILTER_VALIDATE_INT);

    if ($length && $width && $length > 0 && $width > 0) {
        require_once __DIR__ . '/src/Calculated/Calculated.php';
        $cost = \Calculated\Calculated::calculateCost($length, $width);

        $_SESSION['cart'][] = [
            'length' => $length,
            'width' => $width,
            'cost' => $cost
        ];
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['remove'])) {
    $index = filter_input(INPUT_GET, 'remove', FILTER_VALIDATE_INT);
    if ($index !== false && isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1);
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$totalCost = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalCost += $item['cost'];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Калькулятор товаров</title>
</head>
<body>

<div class="input-field">
    <p>Длина товара</p>
    <input type="number" id="length" name="length" min="1">
</div>

<div class="input-field">
    <p>Ширина товара</p>
    <input type="number" id="width" name="width" min="1">
</div>

<div class="input-field">
    <p>Стоимость товара <span id="costDisplay">0</span> руб.</p>

</div>

<button type="button" id="addToCart">Добавить в корзину</button>

<?php if (!empty($_SESSION['cart'])): ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Товар</th>
            <th>Стоимость товара</th>
            <th>Действие</th>
        </tr>
        <?php foreach ($_SESSION['cart'] as $i => $item): ?>
            <tr>
                <td><?= $item['length'] ?>x<?= $item['width'] ?></td>
                <td><?= $item['cost'] ?> руб.</td>
                <td><a href="?remove=<?= $i ?>">Удалить</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p>Итого: <?= $totalCost ?> руб.</p>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const l = document.getElementById('length');
        const w = document.getElementById('width');
        const c = document.getElementById('costDisplay');
        const btn = document.getElementById('addToCart');

        const calc = () => {
            const length = +l.value;
            const width = +w.value;

            if (length && width) {
                fetch('/php/calculateditem.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `length=${length}&width=${width}`
                })
                    .then(r => r.json())
                    .then(d => c.textContent = d.cost)
                    .catch(() => c.textContent = 'Ошибка');
            } else {
                c.textContent = '0';
            }
        };

        l.oninput = calc;
        w.oninput = calc;

        btn.onclick = () => {
            const length = +l.value;
            const width = +w.value;

            if (length && width) {
                const form = document.createElement('form');
                form.method = 'POST';

                ['length', length, 'width', width, 'add_to_cart', 1].forEach((v, i, a) => {
                    if (i % 2 === 0) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = v;
                        input.value = a[i + 1];
                        form.appendChild(input);
                    }
                });

                document.body.appendChild(form);
                form.submit();
            }
        };
    });
</script>
</body>
</html>