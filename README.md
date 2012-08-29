opCsvExportPlugin
=================

SNS 内メンバのデータを csv 形式で出力する OpenPNE のプラグイン

# How to Use

    ./symfony opCsvExport:export

## Options

<table>
<tr>
<th>オプション名</th><th>詳細</th><th>デフォルト値</th>
</tr>
<tr>
<td>from</td><td>メンバID 開始位置 整数値 (inclusive)</td><td>1</td>
</tr>
<tr>
<td>to</td><td>メンバID 終了位置 整数値 (exxlusive)</td><td>なし（最後まで）</td>
</tr>
<tr>
<td>header</td><td>各データ名を csv の最初に付与するか true/false </td><td>true</td>
</tr>
</table>
