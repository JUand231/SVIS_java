package co.edu.sena.cimm.svis.repository;

import co.edu.sena.cimm.svis.model.Token_OTP;
import java.sql.Connection;
import java.sql.SQLException;
import java.time.LocalDateTime;
import java.util.Optional;

public interface TokenOtpRepository {

    void generarYGuardar(Long idEncuesta, Long idUsuario, String codigo, LocalDateTime expiraEn);

    boolean usuarioYaVoto(Long idUsuario, Long idEncuesta);

    String validarYAplicarRegla(String codigo, Long idUsuario, Long idEncuesta); // Retorna "OK" o "Error"

    boolean estaExpirado(String codigo);

    Optional<Token_OTP> buscarPorCodigo(String codigo, Long idUsuario, Long idEncuesta);

    Optional<Token_OTP> buscarPorCodigoForUpdate(Connection c, String codigo, Long idUsuario, Long idEncuesta) throws SQLException;

    void marcarUsado(Connection c, String codigo) throws SQLException;

    java.util.List<co.edu.sena.cimm.svis.model.Token_OTP> listarPorEncuesta(Long encuestaId);
}
