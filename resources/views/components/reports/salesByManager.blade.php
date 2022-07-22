<div class="cell-wrapper">
    <div class="cell-wrapper__caption">
        <p class="caps">Продажи по менеджерам</p>
        <p class="caps">Клиенты в активной работе</p>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th scope="col">Менеджер</th>
            <th scope="col">Кол-во</th>
            <th scope="col">Сумма</th>
            <th scope="col">Бюджет</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items['items'] as $item)
            @if(sizeof($item['users']) > 0)
                <tr>
                <tr>
                    <td>
                        <b>{{ $item['name'] }}</b>
                    </td>
                    <td>
                        <b>{{ $item['count'] }}</b>
                    </td>
                    <td>
                        <b>{{ number_format($item['price'], 2, ',', ' ') }} ₽</b>
                    </td>
                    <td>
                        <b>{{ number_format($item['budget'], 2, ',', ' ') }} ₽</b>
                    </td>
                </tr>
                </tr>
                @foreach($item['users'] as $user)
                    <tr>
                        <td>{{ $user['name'] }}</td>
                        <td>{{ $user['count'] }}</td>
                        <td>
                            {{ number_format($user['price'], 2, ',', ' ') }} ₽
                        </td>
                        <td>
                            {{ number_format($user['budget'], 2, ',', ' ') }} ₽
                        </td>
                    </tr>
                @endforeach
            @endif
        @endforeach
        <tr>
            <td><b>Всего:</b></td>
            <td><b>{{ $items['size']['count'] }}</b></td>
            <td><b>{{ $items['size']['price'] }}</b></td>
            <td><b>{{ $items['size']['budget'] }}</b></td>
        </tr>
        </tbody>
    </table>
</div>
