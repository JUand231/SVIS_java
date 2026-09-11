package co.edu.sena.cimm.svis.repository;

import com.zaxxer.hikari.HikariConfig;
import com.zaxxer.hikari.HikariDataSource;

import javax.sql.DataSource;
import java.io.IOException;
import java.io.InputStream;
import java.sql.Connection;
import java.sql.SQLException;
import java.util.Properties;

public final class BaseDeDatos {

    private static volatile DataSource dataSource;

    private BaseDeDatos() {
    }

    public static DataSource getDataSource() {
        if (dataSource == null) {
            synchronized (BaseDeDatos.class) {
                if (dataSource == null) {
                    dataSource = construir();
                }
            }
        }
        return dataSource;
    }

    public static Connection getConnection() throws SQLException {
        return getDataSource().getConnection();
    }

    private static DataSource construir() {
        Properties p = cargarPropiedades();

        try { Class.forName("com.mysql.cj.jdbc.Driver"); } catch (Exception e) {}
        HikariConfig cfg = new HikariConfig();
        cfg.setDriverClassName("com.mysql.cj.jdbc.Driver");
        cfg.setJdbcUrl(valor(p, "db.url", "SIS_DB_URL",
                "jdbc:mysql://localhost:3306/votaciones?serverTimezone=UTC&useSSL=false&allowPublicKeyRetrieval=true"));
        cfg.setUsername(valor(p, "db.user", "SIS_DB_USER", "root"));
        cfg.setPassword(valor(p, "db.password", "SIS_DB_PASSWORD", ""));
        cfg.setMaximumPoolSize(Integer.parseInt(valor(p, "db.poolSize", "SIS_DB_POOL", "5")));
        cfg.setPoolName("votaciones-pool");
        cfg.setConnectionTimeout(10000);

        return new HikariDataSource(cfg);
    }

    private static Properties cargarPropiedades() {
        Properties p = new Properties();
        try (InputStream in = BaseDeDatos.class.getClassLoader().getResourceAsStream("db.properties")) {
            if (in != null) {
                p.load(in);
            }
        } catch (IOException e) {
            // Si no existe el archivo, usa las variables de entorno o los valores por defecto de arriba.
        }
        return p;
    }

    private static String valor(Properties p, String clave, String envKey, String porDefecto) {
        String v = System.getenv(envKey);
        if (v == null || v.trim().isEmpty()) {
            v = p.getProperty(clave);
        }
        if (v == null || v.trim().isEmpty()) {
            v = porDefecto;
        }
        return v;
    }
}