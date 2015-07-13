<?php

class niunPDO {
	/**
	 *	niunPDO
	 *
	 *	A maneira mais rápida de gerenciar suas conexões.
	 *
	 *	Autor: Niunzin
	 *	Versão: 1.0.0
	 *	Sinta-se livre para editar e redistribuir, sem necessidade de licenças
	 *	Não me responsabilizo por possíveis falhas de segurança, visto que
	 *	é um projeto em prol da comunidade, no entanto, o sistema aparentemente
	 *	é seguro e não apresentou problemas em meus projetos.
	 */

	/**
	 * Nome de usuário do banco de dados MySQL.
	 * @var string
	 */
    private static $DB_USER = '';
	
	/**
	 * Senha do banco de dados MySQL.
	 * @var string
	 */
    private static $DB_PASS = '';
	
	/**
	 * Nome do banco de dados MySQL.
	 * @var string
	 */
    private static $DB_NAME = '';
	
	/**
	 * Host do banco de dados MySQL.
	 * @var string
	 */
    private static $DB_HOST = '';
	
	/**
	 * Prefixo das tabelas (opcional) 
	 * Ex: forum_
	 * @var string
	 */
    const PREFIX = '';
	
    private $PDO;
    private $connected = false;
	
	/**
	 * Construtor da classe
	 * Já inicializa a conexão
	 */
    public function __construct()
    {
        try
        {
            $this->PDO = new PDO('mysql:host=' . self::$DB_HOST . ';dbname=' . self::$DB_NAME,
                self::$DB_USER, self::$DB_PASS
            );
            $this->connected = true;
        } catch (Exception $error)
        {
            echo $error->getMessage();
            return $error->getMessage();
        }
    }

	/**
	 * Retorna a conexão atual
	 *
	 * @return object Conexão
	 */
    public function getConnection()
    {
        return $this->PDO;
    }

	/**
	 * Realiza a operação SELECT
	 * Exemplo de uso:
	 * niunPDO::select($connection, '*', 'usuarios', 'WHERE `id`=?', array(24));
	 * 
	 * @param object $pdo Conexão atual
	 * @param string $what O que selecionar
	 * @param string $from Tabela
	 * @param string $addon Informações adicionais (opcional)
	 * @param array $values Valores (opcional)
	 * @return array $result Resultados encontrados pela query
	 */
    public static function select($pdo, $what, $from, $addon = '', $values = array())
	{
		try
		{
		    $prefix = self::PREFIX;
			$query = $pdo->prepare("SELECT {$what} FROM `{$prefix}{$from}` {$addon}");
			if(!is_array($values)) return 0; // error

			$i = 1;
			foreach($values as $value)
			{
				$query->bindParam($i, $value);
				$i++;
			}
		    $query->execute();
		} catch(PDOException $error) {
			echo $error->getMessage();
			die();
		}

		return $query->fetch();
	}

	/**
	 * Realiza a operação SELECT e retorna todos os resultados
	 * Exemplo de uso:
	 * niunPDO::selectAll($connection, '*', 'usuarios', 'WHERE `id`=?', array(24));
	 * 
	 * @param object $pdo Conexão atual
	 * @param string $what O que selecionar
	 * @param string $from Tabela
	 * @param string $addon Informações adicionais (opcional)
	 * @param array $values Valores (opcional)
	 * @return array $result Resultados encontrados pela query
	 */
	public static function selectAll($pdo, $what, $from, $addon = '', $values = array())
	{
		try
		{
		    $prefix = self::PREFIX;
			$query = $pdo->prepare("SELECT {$what} FROM `{$prefix}{$from}` {$addon}");
			if(!is_array($values)) return 0; // error

			$i = 1;
			foreach($values as $value)
			{
				$query->bindParam($i, $value);
				$i++;
			}
            $query->execute();
			return $query->fetchAll();
		} catch(PDOException $error) {
			echo $error->getMessage();
			die();
		}
		return false;
	}

	/**
	 * Conta valores de uma pesquisa
	 * Exemplo de uso:
	 * niunPDO::count($connection, 'usuarios', 'WHERE `id`=?', array(24));
	 * 
	 * @param object $pdo Conexão atual
	 * @param string $table Tabela
	 * @param string $addon Informações adicionais (opcional)
	 * @param array $values Valores (opcional)
	 * @return int $result Resultado
	 */
	public static function count($pdo, $table, $addon = '', $values = array())
	{
		try
		{
		    $prefix = self::PREFIX;
			$str = "SELECT COUNT(*) FROM `{$prefix}{$table}` {$addon}";
			$res = $pdo->prepare($str);
			if(!is_array($values)) return 0; // error

			$i = 1;
			foreach($values as $value)
			{
				$res->bindParam($i, $value);
				$i++;
			}
		} catch(PDOException $error) {
			echo $error->getMessage();
			die();
		}

		$res->execute();
		return $res->fetchColumn();
	}
	
	/**
	 * Insere um valor no banco de dados
	 * Exemplo de uso:
	 * niunPDO::insert($connection, 'usuarios', 
	 *		array(
	 *			'nome' => 'Niunzin',
	 *			'senha' => '123'
	 *		)
	 *	);
	 * 
	 * @param object $pdo Conexão atual
	 * @param string $where Tabela
	 * @param array $data Campo => Valor
	 * @return bool $result Resultado
	 */
	public static function insert($pdo, $where, $data)
	{
		try
		{
		    $prefix = self::PREFIX;
			$aeho = array();
			$rows = '(';
			$values = 'VALUES(';
			foreach($data as $key => $value)
			{
				$rows .= '`' . $key . '`, ';
				$values .= "?, ";
				array_push($aeho, $value);
			}
			$rows = substr($rows, 0, strlen($rows)-2) . ')';
			$values = substr($values, 0, strlen($values)-2) . ')';

			$query = "INSERT INTO `{$prefix}{$where}` {$rows} {$values}";
			$prepare = $pdo->prepare($query);

			$a = count($data, 1) +1;
			for($i = 1; $i < $a; $i++)
			{
				$prepare->bindParam($i, $aeho[($i-1)]);
			}
		} catch(PDOException $error) {
			echo $error->getMessage();
			die();
		}
		return $prepare->execute();
	}

	/**
	 * Atualiza um valor no banco de dados
	 * Exemplo de uso:
	 * niunPDO::update($connection, 'usuarios', 
	 *		array(
	 *			'nome' => 'Niunzin',
	 *			'senha' => '123'
	 *		),
	 *      'WHERE `id`=?', array(24)
	 *	);
	 * 
	 * @param object $pdo Conexão atual
	 * @param string $where Tabela
	 * @param array $data Campo => Valor
	 * @param string $condition Addons (opcional)
	 * @param array $s_values Valores (opcional)
	 * @return bool $result Resultado
	 */
	public static function update($pdo, $table, $data = array(), $condition, $s_values = array())
	{
		try
		{
		    $prefix = self::PREFIX;
			if(!is_array($data)) return false;
			if(!is_array($s_values)) return false;

			$aeho = array();
			$values = '';
			foreach($data as $key => $value)
			{
				$values .= '`' . $key . '`=?, ';
				array_push($aeho, $value);
			}
			$values = substr($values, 0, strlen($values)-2);

			$query = "UPDATE `{$prefix}{$table}` SET {$values} {$condition}";
			$prepare = $pdo->prepare($query);

			$a = count($data, 1) +1;
			$i_c = 0;
			for($i = 1; $i < $a; $i++)
			{
				$prepare->bindParam($i, $aeho[($i-1)]);
				$i_c = $i;
			}

			foreach($s_values as $val)
			{
				$i_c = $i_c + 1;
				$prepare->bindParam($i_c, $val);
			}
		} catch(PDOException $error) {
			echo $error->getMessage();
			die();
		}
		 return $prepare->execute();
	}

	/**
	 * Remove um valor no banco de dados
	 * Exemplo de uso:
	 * niunPDO::update($connection, 'usuarios', 'WHERE `id`=?', array(24));
	 * 
	 * @param object $pdo Conexão atual
	 * @param string $from Tabela
	 * @param string $addon Addons (opcional)
	 * @param array $values Valores (opcional)
	 * @return bool $result Resultado
	 */
	public static function delete($pdo, $from, $addon, $values = array())
	{
		try
		{
		    $prefix = self::PREFIX;
			$querystr = "DELETE FROM `{$prefix}{$from}` {$addon}";
			$query = $pdo->prepare($querystr);
			if(!is_array($values)) return false;

			$i = 1;
			foreach($values as $value)
			{
				$query->bindParam($i, $value);
				$i++;
			}
		} catch(PDOException $error) {
			echo $error->getMessage();
			die();
		}
		return $query->execute();
	}
}
