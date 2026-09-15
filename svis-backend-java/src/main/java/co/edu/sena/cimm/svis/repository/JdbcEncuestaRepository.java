package co.edu.sena.cimm.svis.repository;

import co.edu.sena.cimm.svis.model.Encuesta;
import co.edu.sena.cimm.svis.model.EstadoEncuesta;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;

public class JdbcEncuestaRepository implements EncuestaRepository {

    private static final String Columnas = "id, titulo, descripcion, estado";

    @Override
    public List<Encuesta> listarActivas() {

        String consulta = "Select " + Columnas + " from encuesta where estado = 'ACTIVA'";
        List<Encuesta> lista = new ArrayList<>();
        try (Connection c = BaseDeDatos.getConnection(); PreparedStatement ps = c.prepareStatement(consulta); ResultSet rs = ps.executeQuery()) {
            while (rs.next()) {
                lista.add(map(rs));
            }
        } catch (SQLException e) {
            throw new RuntimeException("Error consultando encuestas", e);
        }
        return lista;

    }

    @Override
    public List<Encuesta> listarTodas() {
        String consulta = "select " + Columnas + " from encuesta";
        List<Encuesta> lista = new ArrayList<>();
        try (Connection c = BaseDeDatos.getConnection(); PreparedStatement ps = c.prepareStatement(consulta); ResultSet rs = ps.executeQuery()) {
            while (rs.next()) {
                lista.add(map(rs));
            }
        } catch (SQLException e) {
            throw new RuntimeException("Error consultando encuestas", e);
        }
        return lista;
    }

    @Override
    public long crearEncuesta(String titulo, String descripcion) {
        String consulta = "INSERT INTO encuesta(titulo, descripcion, estado) VALUES (?, ?, ?)";
        try (Connection c = BaseDeDatos.getConnection(); PreparedStatement ps = c.prepareStatement(consulta, Statement.RETURN_GENERATED_KEYS)) {

            ps.setString(1, titulo);
            ps.setString(2, descripcion);
            ps.setString(3, "ACTIVA");
            ps.executeUpdate();
            try (ResultSet rs = ps.getGeneratedKeys()) {
                if (rs.next()) {
                    return rs.getLong(1);
                }
            }
            throw new RuntimeException("No se obtuvo el ID generado de la encuesta");

        } catch (SQLException ex) {
            throw new RuntimeException("Error creando la encuesta", ex);
        }
    }

    @Override
    public Encuesta obtenerEncuesta(Long id) {
        String consulta = "select " + Columnas + " from encuesta where id = ?";

        try (Connection c = BaseDeDatos.getConnection(); PreparedStatement ps = c.prepareStatement(consulta)) {

            ps.setLong(1, id);

            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    return map(rs);
                }
            }
        } catch (SQLException e) {
            throw new RuntimeException("Error consultando la encuesta", e);
        }
        throw new RuntimeException("No existe una encuesta con el ID: " + id);
    }

    private Encuesta map(ResultSet rs) throws SQLException {
        String estadoStr = rs.getString("estado");
        EstadoEncuesta estado = Enum.valueOf(EstadoEncuesta.class, estadoStr);

        return new Encuesta(
                rs.getLong("id"),
                rs.getString("titulo"),
                rs.getString("descripcion"),
                estado
        );
    }

}
