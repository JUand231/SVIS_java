package co.edu.sena.cimm.svis.model;

public enum EstadoEncuesta {
    ACTIVA("Encuesta activada, pendiente de cierre"),
    CERRADA("Encuesta cerrada");
    
    private final String descripcion;
    
    EstadoEncuesta(String descripcion){
        this.descripcion = descripcion;
    }
    
    public String getDescripcion() {
        return descripcion;
    }
}
