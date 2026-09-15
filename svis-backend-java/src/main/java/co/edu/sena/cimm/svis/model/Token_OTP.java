package co.edu.sena.cimm.svis.model;

import java.time.LocalDateTime;

public class Token_OTP {
    private Long Id;
    private Long encuesta_id;
    private Long usuario_id;
    private String codigo;
    private String estado;
    private LocalDateTime expira_en;

    public Token_OTP() {
    }

    public Token_OTP(Long Id, Long encuesta_id, Long usuario_id, String codigo, String token, LocalDateTime expira_en) {
        this.Id = Id;
        this.encuesta_id = encuesta_id;
        this.usuario_id = usuario_id;
        this.codigo = codigo;
        this.estado = token;
        this.expira_en = expira_en;
    }

    public Long getId() {
        return Id;
    }

    public void setId(Long Id) {
        this.Id = Id;
    }

    public Long getEncuesta_id() {
        return encuesta_id;
    }

    public void setEncuesta_id(Long encuesta_id) {
        this.encuesta_id = encuesta_id;
    }

    public Long getUsuario_id() {
        return usuario_id;
    }

    public void setUsuario_id(Long usuario_id) {
        this.usuario_id = usuario_id;
    }

    public String getCodigo() {
        return codigo;
    }

    public void setCodigo(String codigo) {
        this.codigo = codigo;
    }

    public String getToken() {
        return estado;
    }

    public void setToken(String token) {
        this.estado = token;
    }

    public String getEstado() {
        return estado;
    }

    public void setEstado(String estado) {
        this.estado = estado;
    }

    public LocalDateTime getExpira_en() {
        return expira_en;
    }

    public void setExpira_en(LocalDateTime expira_en) {
        this.expira_en = expira_en;
    }

}
