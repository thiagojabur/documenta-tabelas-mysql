<html>
<head>
<title>Documentação de Tabelas de uma Base MySQL</title>
</head>
<body>
<style>
table, th, td {
  border: 1px solid black;
  font-family: Arial;
  font-size:9pt;
  border-collapse: collapse;
  padding: 5px;
  
}

p {
  font-family: Arial;
  font-size:9pt;
}
</style>

<?php

$host = "localhost";
$db_name = "";
$username = "";
$pass = "";
    

// Create connection
$conn = new mysqli($host, $username, $pass, $db_name);

// Check connection
if ($conn->connect_error) {
  die("<p>A conexão falhou. Altere as variáveis de conexão no código. Detalhes: " . $conn->connect_error . "</p>");
}


$consulta_tables = $conn->query("show tables;"); 
$i = 0;
while($nome_tabela=$consulta_tables->fetch_row()) {
$i++;

	$q = $conn->query("select column_name as 'Campo', table_schema as 'Banco', data_type as 'Tipo', character_maximum_length as 'Tamanho', column_key as 'Chave' 
	                   from information_schema.columns where table_name = '" . $nome_tabela[0] . "';");

	if($q){
		?>

<br>
<p><b>Tabela <?= $i ?>: <?= $nome_tabela[0] ?></b></p>
<table>
<thead>
        <tr>
            <th scope="col">Chave</th>
			<th scope="col">Campo</th>
            <th scope="col">Tipo</th>
			<th scope="col">Tamanho</th>
			<th scope="col">Banco</th>
			<th scope="col">Campo fonte</th>
        </tr>
		</thead>
		<tbody>	
		<?php		
		while($r=$q->fetch_assoc()) {
			echo "<tr>";
			echo "<td>";
			if ($r["Chave"] == "PRI") echo "Primária";
			$campo_fonte = "";
			if ($r["Chave"] == "MUL") 
			{	
			echo "Estrangeira";
			
			
			$sql_chave_estrangeira = "SELECT referenced_table_name, referenced_column_name
									  FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                                      WHERE 
									  table_name = '" . $nome_tabela[0] . "' and column_name = '" . $r["Campo"] . "';";
			
			$consulta_chave = $conn->query($sql_chave_estrangeira);
			$nome_fonte=$consulta_chave->fetch_row();
			$campo_fonte = $nome_fonte[0] . "." . $nome_fonte[1];
			
			}
			echo "</td>";	
			echo "<td>" . $r["Campo"] . "</td>";		
			echo "<td>" . $r["Tipo"] . "</td>";	
			echo "<td style='text-align: right'>" . $r["Tamanho"] . "</td>";	
			echo "<td>" . $r["Banco"] . "</td>";	
			echo "<td>" . $campo_fonte . "</td>";
			echo "</tr>";
		}
		?>
		</tbody>
		</table>
	<?php
	}	

}


?>