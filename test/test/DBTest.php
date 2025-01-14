<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class DBTest extends TestCase
{
    private static $Db = null;

    public static function setUpBeforeClass(): void
    {
        self::$Db = new DB('root','test','192.168.100.101:3306','webdoc');
    }

    public static function tearDownAfterClass(): void
    {
        self::$Db->cerrarConexion();
    }
    
    public function testCanConstruct(): void{
        $Db = new DB('root','test','127.0.0.1:3306','webdoc');
        $this->assertSame('DB', get_class($Db));
    }

    public function testConexionOk():void{
        $Db = new DB('root','test','192.168.100.101:3306','webdoc');
        $this->assertTrue($Db->isConnected());        
    }

    /* comentada para evitar la pausa que supone la conexión errónea
    public function testConexionError():void{
        $Db = new DB('root','test','192.168.100.101:3307','webdoc');
        $this->assertFalse($Db->isConnected());
    }
    */

    public function testCerrarConexion():void{
        $Db = new DB('root','test','192.168.100.101:3306','webdoc');
        $Db->cerrarConexion();
        $this->assertFalse($Db->isConnected());
    }

    #[Depends('testconexionOk')]
    public function testBaseDatosExisteOk():void{
        $this->assertTrue(self::$Db->existeDB('webdoc'));
    }

    #[Depends('testconexionOk')]
    public function testBaseDatosExisteError():void{
        $this->assertFalse(self::$Db->existeDB('no_webdoc'));
    }

    #[Depends('testconexionOk')]
    public function testSeleccionaBaseDatosOk():void{
        $this->assertTrue(self::$Db->seleccionarBBDD('webdoc'));
    }

    #[Depends('testconexionOk')]
    public function testSeleccionaBaseDatosErr():void{
        $this->assertFalse(self::$Db->seleccionarBBDD('no_webdoc'));
    }

    #[Depends('testconexionOk')]
    public function testEjecutarSql1():void{
        self::$Db->sentenciaSqlParametros("DROP DATABASE TEST",[]);
        $this->assertFalse(self::$Db->seleccionarBBDD('TEST'));
        self::$Db->sentenciaSqlParametros("CREATE DATABASE TEST",[]);
        $this->assertTrue(self::$Db->seleccionarBBDD('TEST'));
        self::$Db->sentenciaSqlParametros("CREATE TABLE Personas (ID int NOT NULL AUTO_INCREMENT, Nombre varchar(255), PRIMARY KEY (`ID`))",[]);
        $id = self::$Db->sentenciaSqlParametros("INSERT INTO Personas (Nombre) VALUE (?)",['Adan']);
        $this->assertEquals(1,$id);
        $id = self::$Db->sentenciaSqlParametros("INSERT INTO Personas (Nombre) VALUE (?)",['Eva']);
        $this->assertEquals(2,$id);
    }
    
    /*
    #[Depends('testconexionOk')]
    public function testEjecutarSql2():void{
        self::$Db->ejecutarSql("DROP DATABASE TEST");
        $this->assertFalse(self::$Db->seleccionarBBDD('TEST'));
        self::$Db->ejecutarSql("CREATE DATABASE TEST");
        $this->assertTrue(self::$Db->seleccionarBBDD('TEST'));
        self::$Db->ejecutarSql("CREATE TABLE Personas (ID int NOT NULL AUTO_INCREMENT, Nombre varchar(255), PRIMARY KEY (`ID`))");
        $id = self::$Db->ejecutarSql("INSERT INTO Personas (Nombre) VALUE ('Adan')");
        $this->assertEquals(1,$id);
        $id = self::$Db->ejecutarSql("INSERT INTO Personas (Nombre) VALUE ('Eva')");
        $this->assertEquals(2,$id);
    }
    */

    #[Depends('testEjecutarSql1')]
    public function testConsultaSql1():void{        
        $res= self::$Db->consultaSqlParametros("SELECT * FROM TEST.Personas WHERE ID=?",[1]);
        $this->assertEquals($res[0]['Nombre'],'Adan');
        $res= self::$Db->consultaSqlParametros("SELECT * FROM TEST.Personas WHERE ID=?",[2]);
        $this->assertEquals($res[0]['Nombre'],'Eva');
    }
    
}