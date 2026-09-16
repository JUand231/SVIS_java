package co.edu.sena.cimm.svis.repository;

import co.edu.sena.cimm.svis.model.Usuario;
import java.util.List;

public interface UsuarioRepository {

    Usuario loginAdmin(String username, String contrasenia);

    Usuario loginVotante(String documento, String numeroFicha);

    boolean existeUsername(String username);

    // Devuelve el usuario o null si no existe (para enlazar candidatos).
    Usuario obtenerPorDocumento(String documento);

    Usuario obtenerPorId(Long id);

    void registrarUsuario(String nombre, String documento, String username, String contrasenia, String rol, Long fichaId);

    List<Long> listarIdsPorRol(String rol);
}
