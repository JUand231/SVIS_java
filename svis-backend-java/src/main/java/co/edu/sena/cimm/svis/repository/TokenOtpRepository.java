package co.edu.sena.cimm.svis.repository;

import java.time.LocalDateTime;

public interface TokenOtpRepository {

    void generarYGuardar(Long idEncuesta, Long idUsuario, String codigo, LocalDateTime expiraEn);

    boolean usuarioYaVoto(Long idUsuario, Long idEncuesta);

    String validarYAplicarRegla(String codigo, Long idUsuario, Long idEncuesta); // Retorna "OK" o "Error"

    boolean estaExpirado(String codigo);
}
