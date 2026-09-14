package co.edu.sena.cimm.svis.model;

public class Ficha {
    private Long id;
    private String numero;
    private String programa;
    private String jornada;

    public Ficha() {
    }

    public Ficha(Long id, String numero, String programa, String jornada) {
        this.id = id;
        this.numero = numero;
        this.programa = programa;
        this.jornada = jornada;
    }

    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public String getNumero() {
        return numero;
    }

    public void setNumero(String numero) {
        this.numero = numero;
    }

    public String getPrograma() {
        return programa;
    }

    public void setPrograma(String programa) {
        this.programa = programa;
    }

    public String getJornada() {
        return jornada;
    }

    public void setJornada(String jornada) {
        this.jornada = jornada;
    }
}
