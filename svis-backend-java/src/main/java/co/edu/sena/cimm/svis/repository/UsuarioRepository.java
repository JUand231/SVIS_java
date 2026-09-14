package co.edu.sena.cimm.svis.repository;

import co.edu.sena.cimm.svis.model.Usuario;

public interface UsuarioRepository {

    Usuario loginAdmin(String username, String contrasenia);

    Usuario loginVotante(String documento, String numeroFicha);

    boolean existeUsername(String username);

    void registrarUsuario(String nombre, String documento, String username, String contrasenia, String rol, Long fichaId);
}
