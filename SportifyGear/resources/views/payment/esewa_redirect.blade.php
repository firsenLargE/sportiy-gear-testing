<!DOCTYPE html>
<html>

<head>
    <title>Redirecting to eSewa...</title>
</head>

<body>
    <form action="https://rc.esewa.com.np/epay/main" method="POST" id="esewaForm">
        <input type="hidden" name="amt" value="{{ $data['amt'] }}">
        <input type="hidden" name="txAmt" value="{{ $data['txAmt'] }}">
        <input type="hidden" name="psc" value="{{ $data['psc'] }}">
        <input type="hidden" name="pdc" value="{{ $data['pdc'] }}">
        <input type="hidden" name="tAmt" value="{{ $data['tAmt'] }}">
        <input type="hidden" name="pid" value="{{ $data['pid'] }}">
        <input type="hidden" name="scd" value="{{ $data['scd'] }}">
        <input type="hidden" name="su" value="{{ $data['su'] }}">
        <input type="hidden" name="fu" value="{{ $data['fu'] }}">
    </form>
    <p>Redirecting to eSewa, please wait...</p>
    <script>
        document.getElementById('esewaForm').submit();
    </script>
</body>

</html>
