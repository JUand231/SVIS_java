package co.edu.sena.cimm.svis.model;

public class Opcion {
    private Long Id;
    private Long id_encuesta;
    private String texto;
    private int votos_total;

    public Opcion() {
    }

    public Opcion(Long Id, Long id_encuesta, String texto, int votos_total) {
        this.Id = Id;
        this.id_encuesta = id_encuesta;
        this.texto = texto;
        this.votos_total = votos_total;
    }

    public Long getId() {
        return Id;
    }

    public void setId(Long Id) {
        this.Id = Id;
    }

    public Long getId_encuesta() {
        return id_encuesta;
    }

    public void setId_encuesta(Long id_encuesta) {
        this.id_encuesta = id_encuesta;
    }

    public String getTexto() {
        return texto;
    }

    public void setTexto(String texto) {
        this.texto = texto;
    }

    public int getVotos_total() {
        return votos_total;
    }

    public void setVotos_total(int votos_total) {
        this.votos_total = votos_total;
    }

  
}
