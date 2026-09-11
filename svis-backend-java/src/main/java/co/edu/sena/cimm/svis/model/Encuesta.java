package co.edu.sena.cimm.svis.model;

public class Encuesta {
    private Long Id;
    private String titulo;
    private String descripcion;
    private EstadoEncuesta estado;

    public Encuesta() {
    }

    public Encuesta(Long id, String titulo, String descripcion, EstadoEncuesta encuesta) {
        this.Id = id;
        this.titulo = titulo;
        this.descripcion = descripcion;
        this.estado = encuesta;
    }

    public Long getId() {
        return Id;
    }

    public void setId(Long id) {
        this.Id = id;
    }

    public String getTitulo() {
        return titulo;
    }

    public void setTitulo(String titulo) {
        this.titulo = titulo;
    }

    public String getDescripcion() {
        return descripcion;
    }

    public void setDescripcion(String descripcion) {
        this.descripcion = descripcion;
    }

    public EstadoEncuesta getEncuesta() {
        return estado;
    }

    public void setEncuesta(EstadoEncuesta encuesta) {
        this.estado = encuesta;
    }
}
