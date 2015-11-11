# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"
VM_DISPLAY_NAME = "PHP7-Securities"
VM_HOSTNAME = "php7.app"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.ssh.password = "vagrant"
  config.ssh.insert_key = false

  config.vm.hostname = VM_HOSTNAME
  config.vm.box = "bento/ubuntu-14.04"
  config.vm.provision :shell, path: "vm-bootstrap.sh", privileged: false, args: "#{ENV['PHP']}"
  config.vm.synced_folder ".", "/home/vagrant",
	group: "www-data", mount_options: ["dmode=775,fmode=664"]
  config.vm.network "forwarded_port", guest: 80, host: 8080, auto_correct: true

  config.vm.provider :virtualbox do |vb|
    vb.customize ["modifyvm", :id, "--name", VM_DISPLAY_NAME]
    vb.name = VM_DISPLAY_NAME
  end

end
