
class ApplicationController < ActionController::Base
  protect_from_forgery with: :exception
end

class UsersController < ApplicationController
  before_action :authenticate_user!

  def update
    if valid_params? && current_user.id == params[:id].to_i
      encrypted_data = encrypt_data(params[:name])

      user = current_user
      user.update(name: encrypted_data[:secure_data])
    end
  end

  protected

  def valid_params?
    return params[:name].present? && params[:id].present? &&
           params[:name].is_a?(String) && valid_integer_id?(params[:id])
  end

  def valid_integer_id?(id)
    return false unless id.is_a?(String)
    Integer(id).is_a?(Integer) rescue false
  end

  def encrypt_data(data)
    key = Rails.application.secret_key_base
    cipher = OpenSSL::Cipher::AES.new(256, :CBC)
    cipher.encrypt
    cipher.key = key
    cipher.iv = iv = cipher.random_iv
    encrypted_data = cipher.update(data) + cipher.final

    return {secure_data: iv + encrypted_data, iv: iv}
  end
end
