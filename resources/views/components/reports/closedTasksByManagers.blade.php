<div class="cell-wrapper">
    <div class="cell-wrapper__caption">
        <p class="caps">Закрыто задач по менеджерам</p>
        <p class="caps">Клиенты без активных сделок</p>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th scope="col">Менеджер</th>
            <th scope="col">Кол-во</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items['items'] as $item)
            @if(sizeof($item['users']) > 0)
                <tr>
                    <td>
                        <b>{{ $item['name'] }}</b>
                    </td>
                    <td>
                        <b>{{ $item['count'] }}</b>
                    </td>
                </tr>
                @foreach($item['users'] as $user)
                    <tr>
                        <td>{{ $user['name'] }}</td>
                        <td>{{ $user['count'] }}</td>
                    </tr>
                @endforeach
            @endif
        @endforeach
        <tr>
            <td><b>Всего:</b></td>
            <td><b>{{ $items['size']['count'] }}</b></td>
        </tr>
        </tbody>
    </table>
</div>
