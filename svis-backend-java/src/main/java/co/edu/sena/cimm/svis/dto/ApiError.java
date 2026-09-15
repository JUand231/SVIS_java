package co.edu.sena.cimm.svis.dto;

public class ApiError {
    public String codigo;
    public String mensaje;

    public ApiError(String codigo, String mensaje) {
        this.codigo = codigo;
        this.mensaje = mensaje;
    }
}
