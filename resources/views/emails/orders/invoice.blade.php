<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice!</title>

<style type="text/css">
    * {
        font-family: Verdana, Arial, sans-serif;
    }
    table{
        font-size: x-small;
    }
    tfoot tr td{
        font-weight: bold;
        font-size: x-small;
    }
    .gray {
        background-color: lightgray
    }
</style>

</head>
<body>

  <table width="100%">
    <tr>
        <td align="right">
            <h3>Assignment 2 PHP 3</h3>
            <pre>
                Company name: Undefined
                Company address: 69 Somewhere on the Earth
                phone: 05489745
            </pre>
        </td>
    </tr>

  </table>

  <table width="100%">
    <tr>
        <td><strong>From:</strong> Linblum - Barrio teatral</td>
        <td><strong>To:</strong> {{ $order->name }}</td>
    </tr>

  </table>

  <br/>

  <table width="100%">
    <thead style="background-color: lightgray;">
        <tr>
            <th>#</th>
            <th>Description</th>
            <th>Quantity</th>
            <th>Unit Price $</th>
            <th>Total $</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($order->products as $product)    
        <tr>
            <th scope="row">{{ $product->id }}</th>
            <td>{{ $product->name }}</td>
            <td align="right">{{ $product->quantity }}</td>
            <td align="right">{{ $product->unit_price }}</td>
            <td align="right">{{ $product->unit_price * $product->quantity }}</td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="3"></td>
            <td align="right">Subtotal $</td>
            <td align="right">{{ $order->total }}</td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td align="right">Tax $</td>
            <td align="right">0</td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td align="right">Total $</td>
            <td align="right" class="gray">$ {{ $order->total }}</td>
        </tr>
    </tfoot>
  </table>
</body>
</html>