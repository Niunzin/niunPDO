# niunPDO
A maneira mais rápida de gerenciar suas conexões.<br>

### Informações
Autor: Niunzin<br>
Versão: 1.0.0<br>

Sinta-se livre para editar e redistribuir, sem necessidade de licenças<br>
Não me responsabilizo por possíveis falhas de segurança, visto que<br>
é um projeto em prol da comunidade, no entanto, o sistema aparentemente<br>
é seguro e não apresentou problemas em meus projetos.

### Exemplo de uso
```
$Database = new niunPDO();
$connection = $Database->getConnection();

niunPDO::insert($connection, 'usuarios',
  array(
    'nome' => 'Niunzin',
    'senha' => '123'
  )
);
```
