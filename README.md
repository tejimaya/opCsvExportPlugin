opCsvExportPlugin
=================

SNS 内メンバのデータを csv 形式で出力する OpenPNE のプラグイン

# How to Use (Web)

sns.example.com というドメインで SNS を動かしているとき，下記のようなURLにアクセスしてください．

    http://sns.exmaple.com/pc_backend.php/monitoring/csvExport/download

フォームに必要な情報を記入して「ダウンロード」ボタンをクリックします．

<table>
<tr>
<th>フォーム名</th><th>詳細</th>
</tr>
<tr>
<td>from</td><td>メンバID 開始位置 整数値 (inclusive)</td>
</tr>
<tr>
<td>to</td><td>メンバID 終了位置 整数値 (inclusive)</td>
</tr>
<tr>
<td>Encode</td><td>ダウンロードするファイルのエンコーディング UTF-8 or SJIS</td>
</tr>
</table>


## Useful way to make easy to access

# How to Use (Task)

    $ ./symfony opCsvExport:export

## Options

<table>
<tr>
<th>オプション名</th><th>詳細</th><th>デフォルト値</th>
</tr>
<tr>
<td>from</td><td>メンバID 開始位置 整数値 (inclusive)</td><td>1</td>
</tr>
<tr>
<td>to</td><td>メンバID 終了位置 整数値 (inclusive)</td><td>なし（最後まで）</td>
</tr>
<tr>
<td>header</td><td>各データ名を csv の最初に付与するか true/false </td><td>true</td>
</tr>
</table>
