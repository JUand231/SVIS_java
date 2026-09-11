package co.edu.sena.cimm.svis.model;

public class Usuario {
    private Long Id;
    private String Username;
    private String contrasenia;
    private String nombre;
    private String documento;
    private String rol;

    public Usuario() {
    }

    public Usuario(Long Id, String Username, String contrasenia, String nombre, String documento, String rol) {
        this.Id = Id;
        this.Username = Username;
        this.contrasenia = contrasenia;
        this.nombre = nombre;
        this.documento = documento;
        this.rol = rol;
    }

    public Long getId() {
        return Id;
    }

    public void setId(Long Id) {
        this.Id = Id;
    }

    public String getUsername() {
        return Username;
    }

    public void setUsername(String Username) {
        this.Username = Username;
    }

    public String getContrasenia() {
        return contrasenia;
    }

    public void setContrasenia(String contrasenia) {
        this.contrasenia = contrasenia;
    }

    public String getNombre() {
        return nombre;
    }

    public void setNombre(String nombre) {
        this.nombre = nombre;
    }

    public String getDocumento() {
        return documento;
    }

    public void setDocumento(String documento) {
        this.documento = documento;
    }

    public String getRol() {
        return rol;
    }

    public void setRol(String rol) {
        this.rol = rol;
    }
    
    
    
}
