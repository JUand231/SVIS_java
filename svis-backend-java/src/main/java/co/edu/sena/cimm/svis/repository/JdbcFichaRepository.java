package co.edu.sena.cimm.svis.repository;

import co.edu.sena.cimm.svis.model.Ficha;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;

public class JdbcFichaRepository implements FichaRepository {

    private static final String Columnas = "id, numero, programa, jornada";

    @Override
    public List<Ficha> listarTodas() {
        String consulta = "select " + Columnas + " from ficha order by numero";
        List<Ficha> lista = new ArrayList<>();
        try (Connection c = BaseDeDatos.getConnection();
             PreparedStatement ps = c.prepareStatement(consulta);
             ResultSet rs = ps.executeQuery()) {
            while (rs.next()) {
                lista.add(map(rs));
            }
        } catch (SQLException e) {
            throw new RuntimeException("Error listando fichas", e);
        }
        return lista;
    }

    @Override
    public Ficha obtenerPorId(Long id) {
        String consulta = "select " + Columnas + " from ficha where id = ?";
        try (Connection c = BaseDeDatos.getConnection();
             PreparedStatement ps = c.prepareStatement(consulta)) {
            ps.setLong(1, id);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    return map(rs);
                }
            }
        } catch (SQLException e) {
            throw new RuntimeException("Error consultando ficha", e);
        }
        throw new RuntimeException("No existe ficha con ID: " + id);
    }

    @Override
    public Ficha obtenerPorNumero(String numero) {
        String consulta = "select " + Columnas + " from ficha where numero = ?";
        try (Connection c = BaseDeDatos.getConnection();
             PreparedStatement ps = c.prepareStatement(consulta)) {
            ps.setString(1, numero);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    return map(rs);
                }
            }
        } catch (SQLException e) {
            throw new RuntimeException("Error consultando ficha por numero", e);
        }
        throw new RuntimeException("No existe ficha con numero: " + numero);
    }

    @Override
    public void crear(String numero, String programa, String jornada) {
        String consulta = "insert into ficha(numero, programa, jornada) values (?, ?, ?)";
        try (Connection c = BaseDeDatos.getConnection();
             PreparedStatement ps = c.prepareStatement(consulta, Statement.RETURN_GENERATED_KEYS)) {
            ps.setString(1, numero);
            ps.setString(2, programa);
            ps.setString(3, jornada);
            ps.executeUpdate();
        } catch (SQLException e) {
            throw new RuntimeException("Error creando ficha (¿numero duplicado?)", e);
        }
    }

    private Ficha map(ResultSet rs) throws SQLException {
        return new Ficha(
                rs.getLong("id"),
                rs.getString("numero"),
                rs.getString("programa"),
                rs.getString("jornada"));
    }
}
