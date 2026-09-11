package co.edu.sena.cimm.svis.repository;

import co.edu.sena.cimm.svis.model.Usuario;

public interface UsuarioRepository {

    Usuario login(String username, String password);

    boolean existeUsername(String username);

    void registrarUsuario(String nombre, String documento, String username, String password, String rol);
}
